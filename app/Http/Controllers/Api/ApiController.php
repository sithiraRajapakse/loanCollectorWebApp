<?php

namespace App\Http\Controllers\Api;

use App\Enums\SchemeType;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\CollectorRepository;
use App\Repositories\CustomerLoanRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\LoanRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ApiController extends Controller
{

    /**
     * @var CustomerRepository
     */
    private $customerRepository;
    /**
     * @var LoanRepository
     */
    private $loanRepository;
    /**
     * @var CollectorRepository
     */
    private $collectorRepository;
    /**
     * @var CustomerLoanRepository
     */
    private $customerLoanRepository;

    /**
     * ApiController constructor.
     *
     * @param CustomerRepository $customerRepository
     * @param LoanRepository $loanRepository
     * @param CollectorRepository $collectorRepository
     * @param CustomerLoanRepository $customerLoanRepository
     */
    public function __construct(CustomerRepository $customerRepository, LoanRepository $loanRepository, CollectorRepository $collectorRepository, CustomerLoanRepository $customerLoanRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->loanRepository = $loanRepository;
        $this->collectorRepository = $collectorRepository;
        $this->customerLoanRepository = $customerLoanRepository;
    }

    /**
     * Log in to the app
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $email = $request->post('email');
        $password = $request->post('password');
        $deviceName = $request->post('device_name');

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        // find the collector user with the email
        $collector = User::where('email', $email)->first();

        // check the password
        if (!$collector || !Hash::check($password, $collector->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // create the token
        $token = $collector->createToken($deviceName)->plainTextToken;
        // return the token in the response
        return response()->json([
            'status' => 200,
            'data' => ['token' => $token]
        ]);
    }

    /**
     * Log out of the app
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        // Revoke the user's current token...
        $user = $request->user();
        $user->currentAccessToken()->delete();
        return response()->json(['status' => 200, 'data' => 'Logged out successfully.']);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        return response()->json(['status' => 200, 'data' => $user]);
    }

    /**
     * Get the list of customers as JSON array
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getCustomers(Request $request)
    {
        $customers = $this->customerRepository->getCustomers();
        if (empty($customers)) {
            return response()->json(['status' => 404, 'data' => 'No customers found.']);
        }

        return response()->json(['status' => 200, 'data' => $customers]);
    }

    /**
     * Get customer by id
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function getCustomerById(Request $request, $id)
    {
        $customer = $this->customerRepository->getCustomerById($id);
        if (empty($customer)) {
            return response()->json(['status' => 404, 'data' => 'Customer not found.']);
        }

        return response()->json(['status' => 200, 'data' => $customer]);
    }

    /**
     * Get the list of loans for customer
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function getLoansForCustomer(Request $request, $id)
    {
        $customer = $this->customerRepository->getCustomerById($id);
        if (empty($customer)) {
            return response()->json(['status' => 404, 'data' => 'Customer not found.']);
        }

        // get loans for the customer
        $loans = $this->customerLoanRepository->getLoansByCustomerId($customer->id);

        $collected = [];
        foreach ($loans as $loan) {
            $collected[] = [
                'id' => $loan->id,
                'customer_id' => $loan->customer->id,
                'customer_name' => ucwords($loan->customer->name),
                'loan_number' => $loan->loan_number,
                'start_date' => $loan->start_date,
                'end_date' => $loan->end_date,
                'loan_amount' => $loan->loan_amount,
                'loan_scheme' => $loan->scheme->title,
                'loan_type' => $loan->scheme->type,
                'total_arrears' => $loan->total_arrears,
            ];
        }

        return response()->json(['status' => 200, 'data' => $collected]);
    }

    /**
     * Get the loan by id
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function getLoanById(Request $request, $id)
    {
        $loan = $this->loanRepository->getLoanById($id);
        if (empty($loan)) {
            return response()->json(['status' => 404, 'data' => 'Loan not found.']);
        }

        return response()->json(['status' => 200, 'data' => $loan]);
    }

    /**
     * Get the list of installments for loan identified by the id
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function getInstallmentsForLoan(Request $request, $id)
    {
        $loan = $this->loanRepository->getLoanById($id);
        if (empty($loan)) {
            return response()->json(['status' => 404, 'data' => 'Loan not found.']);
        }

        $installments = $loan->loanInstallments;
        return response()->json(['status' => 200, 'data' => $installments]);
    }

    /**
     * Get next payable installment for loan
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function getNextPayableInstallmentForLoan(Request $request, $id)
    {
        $loan = $this->loanRepository->getLoanById($id);
        if (empty($loan)) {
            return response()->json(['status' => 404, 'data' => 'Loan not found.']);
        }

        if ($loan->scheme->type == SchemeType::DAILY) {
            $installment = $this->loanRepository->getLeadingPayableInstallment($loan->id);
        } else if ($loan->scheme->type == SchemeType::WEEKLY) {
            $installment = $this->loanRepository->getLeadingPayableInstallment($loan->id);
        } else {
            $installment = null;
        }

        return response()->json(['status' => 200, 'data' => $installment]);
    }

    public function setPaymentForInstallment(Request $request, $id)
    {
        $paid_amount = $request->post('paid_amount');
        $user = $request->user();

        $loanInstallment = $this->loanRepository->getInstallmentById($id);
        if (empty($loanInstallment)) {
            return response()->json(['status' => 'error', 'data' => 'Installment you are trying to make the payment is not available.']);
        }

        if ($loanInstallment->paid_at != null) {
            return response()->json(['status' => 'error', 'data' => 'Payment already made for selected installment.']);
        }

        // make payment
        $arrearsAmount = doubleval($loanInstallment->installment_amount) - doubleval($paid_amount);
        $collector = $this->collectorRepository->getCollectorByUserId($user->id);

        $this->loanRepository->updateInstallment($loanInstallment->id, [
            'paid_amount' => doubleval($paid_amount),
            'paid_at' => date('Y-m-d H:i:s'),
            'arrears_amount' => $arrearsAmount,
            'collector_id' => $collector->id,
        ]);

        // update arrears total for the loan
        $loan = $loanInstallment->loan;
        $loanArrearsTotal = $loan->total_arrears;
        $loan->total_arrears = $loan->total_arrears + $arrearsAmount; // add to the current arrears value of the
        $loan->save(); // make change to the total_arrears value of the loan

        // if the customer has paid more than the installment amount
        if ($arrearsAmount < 0) {
            // get arrears installments in the order of installment order
            $arrearsInstallments = $this->loanRepository->getArrearsInstallments($loanInstallment->loan->id);

            $remainingArrearsAmount = $arrearsAmount;

            // settle arrears from the last installments
            foreach ($arrearsInstallments as $arrearsInstallment) {
                if ($remainingArrearsAmount >= $arrearsInstallments->arrears_amount) {
                    // settle each installment if the remainder falls under the arrears amount for the installment
                    $arrearsInstallment->arrears_settlement_amount = $arrearsInstallments->arrears_amount;
                    $arrearsInstallment->arrears_settled_at = date('Y-m-d H:i:s');
//                    $arrearsInstallment->arrears_amount = 0;
                    $arrearsInstallment->save();

                    // calculate and update
                    //A huge four-way fight ensues, with Stroll getting past his team mate, and Sainz managing to follow him through. Alonso loses out, and he can't quite get past his old foe Vettel after locking up.  the remaining arrears amount after this installment arrears amount
                    $remainingArrearsAmount = $remainingArrearsAmount - $arrearsInstallments->arrears_amount;
                }
            }
        }


        $successMessage = sprintf('Payment of Rs.%s for the installment %d completed successfully.', number_format($paid_amount, 2), $loanInstallment->index_number);
        return response()->json(['status' => 'success', 'data' => $successMessage]);
    }

    /**
     * Get the installment
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function getPayableTotalForTodayByCustomerId(Request $request, $id)
    {
        $customer = $this->customerRepository->getCustomerById($id);
        if(empty($customer)) {
            return response()->json(['status' => 404, 'data' => 'Customer not found.']);
        }

        $installment = $this->loanRepository->getPayableAmountForTodayByCustomerId($customer->id);
        if(empty($installment)) {
            return response()->json(['status' => 404, 'data' => 'Installment not found.']);
        }
        return response()->json(['status' => 'success', 'data' => $installment]);
    }

    public function createMonthlyLoanPayment(Request $request, $id)
    {
        $loan = $this->loanRepository->getLoanById($id);
        if (empty($loan)) {
            return response()->json(['status' => 404, 'data' => 'Loan not found. Please try again with a different loan entry.']);
            return redirect()->route('customer-loans.list')->with('error', 'Loan not found. Please try again with a different loan entry.');
        }

        $data = $request->post();
        $validator = Validator::make($data, [
            "amount" => "required|numeric",
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 503, 'data' => 'Error in input.']);
        }

        // payment date (current date)
        $payment_date = date('Y-m-d');

        // calculate
        $interestAmount = ($loan->loan_amount / 100) * $loan->interest_rate;
        $amountDeductable = $data['amount'] - $interestAmount;
        $arrearsAmount = $amountDeductable < 0 ? $amountDeductable : 0.0;

        // get collector
        $user = auth()->user();
        $collector = $this->collectorRepository->getCollectorByUserId($user->id);

        // create installment
        $indexNumber = $this->loanRepository->getNextInstallmentIndexNo($loan->id);
        $installmentData = [
            'loan_id' => $loan->id,
            'index_number' => $indexNumber,
            'due_date' => date('Y-m-d H:i:s', strtotime($payment_date)),
            'paid_amount' => doubleval($data['amount']),
            'paid_at' => date('Y-m-d H:i:s'),
            'collector_id' => $collector->id,
            'interest_amount' => $interestAmount,
            'installment_amount' => ($amountDeductable > 0) ? $amountDeductable : 0,
            'arrears_amount' => $arrearsAmount,
        ];
        $installment = $this->loanRepository->createInstallment($installmentData);

        // TODO: make adjustment to overall arrears amount
        return response()->json(['status' => 'success', 'data' => $installment]);
    }

    /**
     * Get loan symmary by the loan identified by the id
     * 
     * @param  Request $request
     * @param  string|int  $id      Loan id
     * @return Response $response
     */
    public function getLoanSummaryById(Request $request, $id)
    {
        $loan = $this->loanRepository->getLoanById($id);
        if (empty($loan)) {
            return redirect()->route('loans.register')->with('error', 'Loan not found.');
        }

        $installments = $this->loanRepository->getInstallments($loan->id);

        $dueDates = $installments->pluck('due_date');

        // if($loan->scheme->type === SchemeType::MONTHLY) {
        //     $dueAmount = $this->_calculateDueAmount($loan);
        // }


        // calculate total loan amount
        $loanTotal = $loan->loan_amount + $loan->interest_total;
        // calculate total paid
        $totalPaid = $loan->loanInstallments->where('paid_at', '!=',  null)->sum('paid_amount');
        // total arrears
        $totalArrears = $loan->arrearsTotal();
        // calculate total due amount
        $totalDue = $loanTotal - $totalPaid;

        // create response using the above data
        $responseData = [
            'loan_id' => $loan->id,
            'loan_number' => $loan->loan_number,
            'customer_id' => $loan->customer->id,
            'customer_name' => $loan->customer->name,
            'customer_telephone' => $loan->customer->telephone,
            'start_date' => $loan->start_date,
            'end_date' => $loan->end_date,
            'interest_rate' => number_format($loan->interest_rate, 1),
            'loan_amount' => number_format($loan->loan_amount, 2),
            'interest_total' => number_format($loan->interest_total, 2),
            'loan_total' => number_format($loanTotal, 2),
            'total_paid' => number_format($totalPaid, 2),
            'total_arrears' => number_format(abs($totalArrears), 2),
            'total_due_amount' => number_format($totalDue, 2),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $responseData,
        ]);
    }

}
