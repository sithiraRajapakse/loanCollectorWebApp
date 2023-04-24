<?php

namespace App\Http\Controllers;

use App\Enums\LastInstallmentType;
use App\Enums\SchemeType;
use App\Repositories\CustomerRepository;
use App\Repositories\LoanRepository;
use App\Repositories\SchemeRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class LoanController extends Controller
{
    /**
     * @var CustomerRepository
     */
    private $customerRepository;
    /**
     * @var SchemeRepository
     */
    private $schemeRepository;
    /**
     * @var LoanRepository
     */
    private $loanRepository;

    /**
     * LoanController constructor.
     * @param CustomerRepository $customerRepository
     * @param SchemeRepository $schemeRepository
     * @param LoanRepository $loanRepository
     */
    public function __construct(
        CustomerRepository $customerRepository,
        SchemeRepository $schemeRepository,
        LoanRepository $loanRepository
    )
    {
        $this->middleware('auth');
        $this->customerRepository = $customerRepository;
        $this->schemeRepository = $schemeRepository;
        $this->loanRepository = $loanRepository;
    }


    /**
     * List the existing loans by loan numbers
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $customers = $this->customerRepository->getCustomers();
        $schemes = $this->schemeRepository->getAll();
        $loans = $this->loanRepository->getAllLoans();

        return view('loans.index', compact('customers', 'schemes', 'loans'));
    }

    public function create(Request $request)
    {
        $data = $request->post();

        $validator = Validator::make($data, [
            'customer_id' => 'required|exists:customers,id',
            'scheme_id' => 'required|exists:schemes,id',
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
            'loan_amount' => 'required|numeric',
            'interest_rate' => 'required|between:0,100',
            'no_of_installments' => 'required',
            'installment_amount' => 'required',
            'last_installment_type' => 'required',
        ]);

        // if validator fails, show validation errors with input values set.
        if ($validator->fails()) {
            // get the selected customer and send in flash data
            $customer = $this->customerRepository->getCustomerById($data['customer_id']);
            $validationCustomer = ['id' => $customer->id, 'name' => $customer->name, 'nic_no' => $customer->nic_no, 'telephone' => $customer->telephone];
            return redirect()->route('loans.register')
                ->withInput()
                ->withErrors($validator)
                ->with('error', 'Failed to register the loan in the system. Please check your inputs and try again.')
                ->with('customer', json_encode($validationCustomer));
        }

        $loanAmount = doubleval($data['loan_amount']);
        $interestRate = doubleval($data['interest_rate']);
        $interestTotal = round($loanAmount / 100 * $interestRate);

        $scheme = $this->schemeRepository->getById($data['scheme_id']);
        $isRegularLoan = $scheme->type != SchemeType::CUSTOM();

        $user = auth()->user();

        // prepare dataset
        $loan = [
            'date' => date('Y-m-d'),
            'loan_number' => $this->getNextLoanNumber(),
            'customer_id' => $data['customer_id'],
            'scheme_id' => $data['scheme_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'loan_amount' => $loanAmount,
            'interest_rate' => $interestRate,
            'interest_total' => $interestTotal,
            'created_by_id' => $user->id,
            'no_of_installments' => $data['no_of_installments'],
            'installment_amount' => $data['installment_amount'],
            'last_installment_type' => $data['last_installment_type'],
        ];
        if ($isRegularLoan) {
            $loan['confirmed_at'] = date('Y-m-d H:i:s');
        }

        // send to repository
        $loan = $this->loanRepository->registerLoan($loan);
        if ($loan && $isRegularLoan) {
            $installments = $this->getGeneratedInstallmentsForLoan($loan);
            $this->loanRepository->registerInstallmentsForLoan($loan, $installments);

            // redirect to loan register page
            return redirect()->route('loans.register')->with('success', 'Loan registered successfully.');
        } else {
            // show the installments editor for the custom loan
            return redirect()->route('loan.installments.edit', $loan->id)->with('success', 'Loan registered partially. Please confirm the installments. Make any changes to the installments.');
        }

        return redirect()->route('loans.create')->with('error', 'Failed to register the loan. Please try again.');
    }

    private function getNextLoanNumber()
    {
        $currentYear = date('Y');
        $lastLoanNumber = $this->loanRepository->getLatestLoanNumberByYear($currentYear);

        if (empty($lastLoanNumber)) {
            return sprintf('%d1', $currentYear);
        }

        $lastNumber = substr($lastLoanNumber, 4);

        $nextNumber = intval($lastNumber) + 1;
        return sprintf('%d%d', $currentYear, $nextNumber);
    }

    private function getGeneratedInstallmentsForLoan($loan)
    {
        $scheme = $this->schemeRepository->getById($loan->scheme_id);

        if ($scheme->type == SchemeType::CUSTOM) {
            return false;
        }

        $intervalType = 'days';
        switch ($scheme->type) {
            case SchemeType::DAILY :
                {
                    $intervalType = 'days';
                }
                break;
            case SchemeType::WEEKLY :
                {
                    $intervalType = 'weeks';
                }
                break;
            case SchemeType::MONTHLY :
                {
                    $intervalType = 'months';
                }
                break;
        }

        $interval = sprintf('1 %s', $intervalType);

        // get the installment dates by type, installment count, start date and end date
        $period = CarbonPeriod::create($loan->start_date, $interval, $loan->end_date);

        $deductingTotal = doubleval($loan->loan_amount) + doubleval($loan->interest_total);

        $installments = [];

        $installmentNumber = 1;
        foreach ($period as $key => $date) {
            $thisInstallment = ($deductingTotal > $loan->installment_amount) ? $loan->installment_amount : $deductingTotal;
            $date = $date->format('Y-m-d');

            if ($installmentNumber <= $loan->no_of_installments && $deductingTotal > 0) {
                // check if the last installment
                if ($installmentNumber == $loan->no_of_installments) {
                    // this is the last installment
                    if ($loan->last_installment_type == LastInstallmentType::ADD_TO_PREVIOUS) {
                        // add to previous installment method selected
                        $lastAmount = $installments[$installmentNumber - 1]['amount'];
                        $installments[$installmentNumber - 1]['amount'] = $lastAmount + $thisInstallment;
                    } else {
                        // single installment method selected
                        $installments['installment_' . $installmentNumber] = [
                            'number' => $installmentNumber,
                            'dueDate' => $date,
                            'amount' => $thisInstallment,
                        ];
                    }
                } else {
                    // not last installment
                    $installments['installment_' . $installmentNumber] = [
                        'number' => $installmentNumber,
                        'dueDate' => $date,
                        'amount' => $thisInstallment,
                    ];
                }
            }

            // deduct this installment from the total amount
            $deductingTotal = ($deductingTotal - $thisInstallment);

            $installmentNumber++;
        }
        return $installments;
    }

}
