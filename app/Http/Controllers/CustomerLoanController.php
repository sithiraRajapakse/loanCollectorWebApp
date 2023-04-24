<?php

namespace App\Http\Controllers;

use App\Enums\LastInstallmentType;
use App\Enums\SchemeType;
use App\Models\Scheme;
use App\Repositories\CustomerLoanInstallmentRepository;
use App\Repositories\CustomerLoanRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\HolidayRepository;
use App\Repositories\LoanRepository;
use App\Repositories\SchemeRepository;
use BenSampo\Enum\Exceptions\InvalidEnumKeyException;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerLoanController extends Controller
{
    /**
     * @var CustomerRepository
     */
    private $customerRepository;
    /**
     * @var CustomerLoanRepository
     */
    private $customerLoanRepository;
    /**
     * @var CustomerLoanInstallmentRepository
     */
    private $customerLoanInstallmentRepository;
    /**
     * @var HolidayRepository
     */
    private $holidayRepository;
    /**
     * @var SchemeRepository
     */
    private $schemeRepository;
    /**
     * @var LoanRepository
     */
    private $loanRepository;

    private $holidaysList;

    /**
     * CustomerLoanController constructor.
     * @param CustomerRepository $customerRepository
     * @param CustomerLoanRepository $customerLoanRepository
     * @param HolidayRepository $holidayRepository
     * @param SchemeRepository $schemeRepository
     * @param LoanRepository $loanRepository
     */
    public function __construct(CustomerRepository $customerRepository, CustomerLoanRepository $customerLoanRepository,
                                HolidayRepository $holidayRepository, SchemeRepository $schemeRepository, LoanRepository $loanRepository)
    {
        $this->middleware('auth');
        $this->customerRepository = $customerRepository;
        $this->customerLoanRepository = $customerLoanRepository;
        $this->holidayRepository = $holidayRepository;
        $this->schemeRepository = $schemeRepository;
        $this->loanRepository = $loanRepository;
        $this->_initialize();
    }

    protected function _initialize()
    {
        $this->holidaysList = $this->holidayRepository->list();
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $customers = $this->customerRepository->getCustomers();
        $schemes = $this->schemeRepository->getAll();
        $loans = $this->customerLoanRepository->getAll();

        return view('loans.index', compact('customers', 'schemes', 'loans'));
//        return view('customer-loans.list');
    }

    /**
     * Get loan details as JSON response
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function getLoanDetailsJson(Request $request, $id): JsonResponse
    {
        $loan = $this->loanRepository->getLoanById($id);
        if (empty($loan)) {
            return response()->json([
                'status' => 'error',
                'data' => 'Loan not found.'
            ]);
        }

        $loanTotal = $loan->loan_amount + $loan->interest_total;
        $totalPaid = $loan->loanInstallments->where('paid_at', '!=', null)->sum('paid_amount');

        $data = [
            'loan_number' => $loan->loan_number,
            'customer' => sprintf('<a href="%s">%s</a> (<small>%s</small>)', route('customers.edit', $loan->customer->id), $loan->customer->name, $loan->customer->telephone),
            'duration' => sprintf('<strong>From:</strong> %s <strong>To:</strong> %s', $loan->start_date, $loan->end_date),
            'interest_rate' => $loan->interest_rate . '%',
            'loan_amount' => 'Rs. ' . number_format($loan->loan_amount, 2),
            'interest_total' => 'Rs. ' . number_format($loan->interest_total, 2),
            'loan_total' => 'Rs. ' . number_format($loan->$loanTotal, 2),
            'total_paid' => 'Rs. ' . number_format($totalPaid, 2),
            'total_arrears' => 'Rs. ' . number_format($loan->arrearsTotal(), 2),
            'due_amount' => 'Rs. ' . number_format($loanTotal - $totalPaid, 2),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    /**
     * @return string
     */
    private function getNextLoanNumber(): string
    {
        $currentYear = date('Y');
        $query = $this->customerLoanRepository->getLatestLoanNumberByYear($currentYear);

        if (empty($query)) {
            return sprintf('%d1', $currentYear);
        }

        $lastLoanNumber = $query->loan_number;

//        dump('last loan number is ' . $lastLoanNumber);

        $lastNumber = substr($lastLoanNumber, 4);

        $nextNumber = intval($lastNumber) + 1;
        return sprintf('%d%d', $currentYear, $nextNumber);
    }

    /**
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return mixed
     */
    protected function _getHolidaysForPeriod(Carbon $startDate, Carbon $endDate)
    {
        return $this->holidayRepository->betweenPeriod($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));
    }

    protected function _getMonthsForThePeriod($startDate, $endDate)
    {
        $period = CarbonPeriod::create($startDate, '1 month', $endDate);
        $months = [];
        foreach ($period as $dt) {
            $months[] = $dt->format("Y-m-d");
        }
        return $months;
    }

    protected function _getWeeksForThePeriod($startDate, $endDate)
    {
        $period = CarbonPeriod::create($startDate, '1 week', $endDate);
        $months = [];
        foreach ($period as $dt) {
            $months[] = $dt->format("Y-m-d");
        }
        return $months;
    }

    protected function _getBiWeeksForThePeriod($startDate, $endDate)
    {
        $period = CarbonPeriod::create($startDate, '2 weeks', $endDate);
        $months = [];
        foreach ($period as $dt) {
            $months[] = $dt->format("Y-m-d");
        }
        return $months;
    }

    /**
     * Get installments data as JSON using the given request data
     * @param Request $request
     * @return JsonResponse
     */
    public function getInstallmentsJSON(Request $request): JsonResponse
    {
        $loan_amount = $request->post('loan_amount');
        $interest_rate = $request->post('interest_rate');
        $start_date = $request->post('start_date');
        $end_date = $request->post('end_date');
        $installments_count = $request->post('installments_count');
        $installment_amount = $request->post('installment_amount');
        $type = $request->post('type');
        $_retData = [
            'start_date' => $start_date,
            'end_date' => $start_date,
            'installments' => [],
        ];
        switch ($type) {
            case SchemeType::DAILY:
                {
                    $_retData = $this->_getDailyInstallments($start_date, $end_date, $loan_amount, $interest_rate, $installments_count, $installment_amount);
                }
                break;
            case SchemeType::WEEKLY:
                {
                    $_retData = $this->_getWeeklyInstallments($start_date, $end_date, $loan_amount, $interest_rate, $installments_count, $installment_amount);
                }
                break;
//            case SchemeType::MONTHLY: {
//
//            } break;
        };

        return response()->json(['status' => 'success', 'data' => $_retData]);
    }

    /**
     * Show daily loan create view
     *
     * @return Application|Factory|View
     * @throws InvalidEnumKeyException
     */
    public function dailyLoanView()
    {
        $customerList = $this->customerRepository->getCustomers();
        $scheme = $this->schemeRepository->getByType(SchemeType::fromKey(SchemeType::DAILY));
        $holidays = $this->holidayRepository->list();
        return view('customer-loans.types.daily', compact('customerList', 'scheme', 'holidays'));
    }

    /**
     * Create a new daily loan entry with the installments
     * @param Request $request
     * @return RedirectResponse
     */
    public function createDailyLoan(Request $request)
    {
        $data = $request->post();

        // validate
        $validator = Validator::make($data, [
            'scheme_id' => 'required|integer|exists:schemes,id',
            'customer_id' => 'required|integer|exists:customers,id',
            'loan_amount' => 'required|numeric',
            'interest_rate' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'installments_count' => 'required|integer',
            'installment_amount' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return redirect()->route('customer-loans.daily')->withErrors($validator)->withInput();
        }

        $_retData = $this->_getDailyInstallments($data['start_date'], $data['end_date'], $data['loan_amount'], $data['interest_rate'], $data['installments_count'], $data['installment_amount']);

        // create loan
        $loanNumber = $this->getNextLoanNumber();
//        dd($loanNumber);
        $loan = $this->customerLoanRepository->create([
            'date' => date('Y-m-d'),
            'loan_number' => $loanNumber,
            'customer_id' => $data['customer_id'],
            'scheme_id' => $data['scheme_id'],
            'start_date' => $_retData['start_date'],
            'end_date' => $_retData['end_date'],
            'loan_amount' => $data['loan_amount'],
            'interest_rate' => $data['interest_rate'],
            'interest_total' => $_retData['interest_total'],
            'no_of_installments' => intval($_retData['installments_count']),
            'installment_amount' => $data['installment_amount'],
            'last_installment_type' => LastInstallmentType::SINGLE,
        ]);

        $_installments = [];
        $ind = 1;
        foreach ($_retData['installments'] as $installmentDate => $installmentValue) {
            $intAm = ($installmentValue / 100) * $data['interest_rate'];
            $_installments[] = [
                'loan_id' => $loan->id,
                'index_number' => $ind,
                'due_date' => $installmentDate,
                'installment_amount' => $installmentValue,
                'interest_amount' => $intAm,
            ];
            $ind++;
        }

        $this->customerLoanRepository->createInstallments($loan->id, $_installments);

        return redirect()->route('customer-loans.daily')->with('success', 'Loan created successfully.');
    }

    /**
     * @param $date
     * @return bool
     */
    protected function _isHoliday($date): bool
    {
        $holidays = $this->holidaysList->pluck('date')->toArray();
        return in_array($date, $holidays);
    }

    /**
     * Get the installments details for the daily loan
     *
     * @param $start_date
     * @param $end_date
     * @param $loan_amount
     * @param $interest_rate
     * @param $installments_count
     * @param $installment_amount
     * @return array
     */
    protected function _getDailyInstallments($start_date, $end_date, $loan_amount, $interest_rate, $installments_count, $installment_amount): array
    {
        $startDate = Carbon::parse($start_date);
        $endDate = Carbon::parse($startDate)->addDays($installments_count);
        $monthsCount = ceil($installments_count / 30);

        $totalInterestRate = ($monthsCount * doubleval($interest_rate));
        $totalInterestAmount = ((doubleval($loan_amount) / 100) * $totalInterestRate);
        $totalPayableAmount = doubleval($loan_amount) + $totalInterestAmount;
        $remainder = $totalPayableAmount % $installment_amount;

        $installmentsCount = ($totalPayableAmount - $remainder) / doubleval($installment_amount);
        if ($remainder > 0) {
            // add one to the installments count if remainder is greater than zero
            $installmentsCount++;
        }

        // calculate the proper end date with holidays in mind
        $calcData = $this->_getCalculatedDaysForInstallments($startDate, $installmentsCount);
        $holidays = $calcData['holidays'];
        $endDate = Carbon::parse($calcData['end_date']);
        $installments = [];

        // set correct values in the installments
        $isFirst = true;
        foreach ($calcData['installments'] as $_installment) {
            $_amount = ($isFirst ? (($remainder > 0) ? $remainder : doubleval($installment_amount)) : doubleval($installment_amount));
            $installments["{$_installment['date']}"] = $_amount;
            if ($isFirst) {
                $isFirst = false;
            }
        }

        return [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'months_count' => $monthsCount,
            'loan_amount' => $loan_amount,
            'total_loan_amount' => $totalPayableAmount,
            'interest_rate' => $interest_rate,
            'interest_total' => $totalInterestAmount,
            'installments_count' => $installmentsCount,
            'first_installment_amount' => (($remainder > 0) ? $remainder : $installment_amount),
            'installment_amount' => $installment_amount,
            'holidays' => $holidays,
            'installments' => $installments,
        ];
    }

    /**
     * @param $startDate
     * @param $installmentsCount
     * @return array
     */
    private function _getCalculatedDaysForInstallments($startDate, $installmentsCount): array
    {
        // get the installments tentative start and end dates
        $start_date = Carbon::parse($startDate);
        $end_date = Carbon::parse($startDate);

        // calculate the days count
        $day = 0;
        $holidays = [];
        $installments = [];

        // count days excluding holidays
        while ($day < $installmentsCount) {
            if ($day >= $installmentsCount) {
                break;
            }

            $td = $end_date->copy();
            if (!$this->_isHoliday($td->format('Y-m-d'))) {
                $installments[] = [
                    'installment' => $day + 1,
                    'date' => $td->format('Y-m-d'),
                    'type' => 'day',
                    'amount' => 0
                ];
                $day++;
            } else {
                $holidays[] = $td->format('Y-m-d');
            }

            if ($day < $installmentsCount) {
                $end_date->addDay();
            }
        }
        return [
            'start_date' => $start_date->format('Y-m-d'),
            'end_date' => $end_date->format('Y-m-d'),
            'days' => $day,
            'installments' => $installments,
            'holidays' => $holidays,
        ];
    }

    /**
     * Weekly loan view
     * @return Application|Factory|View
     * @throws InvalidEnumKeyException
     */
    public function weeklyLoanView()
    {
        $customerList = $this->customerRepository->getCustomers();
        $scheme = $this->schemeRepository->getByType(SchemeType::fromKey(SchemeType::WEEKLY));
        $holidays = $this->holidayRepository->list();
        return view('customer-loans.types.weekly', compact('customerList', 'scheme', 'holidays'));
    }

    /**
     * @param $startDate
     * @param $installmentsCount
     * @return array
     */
    private function _getCalculatedWeeksForInstallments($startDate, $installmentsCount): array
    {
        $start_date = Carbon::parse($startDate);
        $end_date = $startDate->copy();
        // add weeks (minus 1 to correct the first date is first week problem)
        $end_date->addWeeks($installmentsCount - 1);
        $weeksPeriod = CarbonPeriod::create($start_date, '1 week', $end_date);
        $day = 0;
        $installments = [];
        $holidays = [];
        foreach ($weeksPeriod as $weekDate) {
            $td = $weekDate;
            $formattedDate = $td->format('Y-m-d');

            if ($this->_isHoliday($td->format('Y-m-d'))) {
                while ($this->_isHoliday($td->format('Y-m-d'))) {
                    $holidays[] = $td->format('Y-m-d');
                    $td->addDay();
                }
            }

            $installments[] = [
                'installment' => $day + 1,
                'date' => $td->format('Y-m-d'),
                'type' => 'day',
                'amount' => 0
            ];
            $day++;
        }

        return [
            'start_date' => $start_date->format('Y-m-d'),
            'end_date' => $end_date->format('Y-m-d'),
            'days' => $day,
            'installments' => $installments,
            'holidays' => $holidays,
        ];
    }

    /**
     * Get the installments details for the daily loan
     *
     * @param $start_date
     * @param $end_date
     * @param $loan_amount
     * @param $interest_rate
     * @param $installments_count
     * @param $installment_amount
     * @return array
     */
    protected function _getWeeklyInstallments($start_date, $end_date, $loan_amount, $interest_rate, $installments_count, $installment_amount): array
    {
        $startDate = Carbon::parse($start_date);
        $endDate = Carbon::parse($startDate)->addDays($installments_count);
        $monthsCount = ceil($installments_count / 4);

        $totalInterestRate = ($monthsCount * doubleval($interest_rate));
        $totalInterestAmount = ((doubleval($loan_amount) / 100) * $totalInterestRate);
        $totalPayableAmount = doubleval($loan_amount) + $totalInterestAmount;
        $remainder = $totalPayableAmount % $installment_amount;

        $installmentsCount = ($totalPayableAmount - $remainder) / doubleval($installment_amount);
        if ($remainder > 0) {
            // add one to the installments count if remainder is greater than zero
            $installmentsCount++;
        }

        // calculate the proper end date with holidays in mind
        $calcData = $this->_getCalculatedWeeksForInstallments($startDate, $installmentsCount);

        $holidays = $calcData['holidays'];
        $endDate = Carbon::parse($calcData['end_date']);
        $installments = [];

        // set correct values in the installments
        $isFirst = true;
        foreach ($calcData['installments'] as $_installment) {
            $_amount = ($isFirst ? (($remainder > 0) ? $remainder : doubleval($installment_amount)) : doubleval($installment_amount));
            $installments["{$_installment['date']}"] = $_amount;
            if ($isFirst) {
                $isFirst = false;
            }
        }

        return [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'months_count' => $monthsCount,
            'loan_amount' => $loan_amount,
            'total_loan_amount' => $totalPayableAmount,
            'interest_rate' => $interest_rate,
            'interest_total' => $totalInterestAmount,
            'installments_count' => $installmentsCount,
            'first_installment_amount' => (($remainder > 0) ? $remainder : $installment_amount),
            'installment_amount' => $installment_amount,
            'holidays' => $holidays,
            'installments' => $installments,
        ];
    }

    /**
     * Create weekly loan and installments list
     * @param Request $request
     * @return RedirectResponse
     */
    public function createWeeklyLoan(Request $request): RedirectResponse
    {
        $data = $request->post();

        // validate
        $validator = Validator::make($data, [
            'scheme_id' => 'required|integer|exists:schemes,id',
            'customer_id' => 'required|integer|exists:customers,id',
            'loan_amount' => 'required|numeric',
            'interest_rate' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'installments_count' => 'required|integer',
            'installment_amount' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return redirect()->route('customer-loans.weekly')->withErrors($validator)->withInput();
        }

        $_retData = $this->_getWeeklyInstallments($data['start_date'], $data['end_date'], $data['loan_amount'], $data['interest_rate'], $data['installments_count'], $data['installment_amount']);

//        dd($_retData);

        // create loan
        $loanNumber = $this->getNextLoanNumber();
//        dd($loanNumber);
        $loan = $this->customerLoanRepository->create([
            'date' => date('Y-m-d'),
            'loan_number' => $loanNumber,
            'customer_id' => $data['customer_id'],
            'scheme_id' => $data['scheme_id'],
            'start_date' => $_retData['start_date'],
            'end_date' => $_retData['end_date'],
            'loan_amount' => $data['loan_amount'],
            'interest_rate' => $data['interest_rate'],
            'interest_total' => $_retData['interest_total'],
            'no_of_installments' => intval($_retData['installments_count']),
            'installment_amount' => $data['installment_amount'],
            'last_installment_type' => LastInstallmentType::SINGLE,
        ]);

        $_installments = [];
        $ind = 1;
        foreach ($_retData['installments'] as $installmentDate => $installmentValue) {
            $intAm = ($installmentValue / 100) * $data['interest_rate'];
            $_installments[] = [
                'loan_id' => $loan->id,
                'index_number' => $ind,
                'due_date' => $installmentDate,
                'installment_amount' => $installmentValue,
                'interest_amount' => $intAm,
            ];
            $ind++;
        }

        $this->customerLoanRepository->createInstallments($loan->id, $_installments);

        return redirect()->route('customer-loans.weekly')->with('success', 'A new Weekly Loan created successfully.');
    }

    /**
     * Show monthly loan create view
     * @return Application|Factory|View
     * @throws InvalidEnumKeyException
     */
    public function monthlyLoanView()
    {
        $customerList = $this->customerRepository->getCustomers();
        $scheme = $this->schemeRepository->getByType(SchemeType::fromKey(SchemeType::MONTHLY));
        $holidays = $this->holidayRepository->list();
        return view('customer-loans.types.monthly', compact('customerList', 'scheme', 'holidays'));
    }

    /**
     * Create monthly loan
     * @param Request $request
     * @return RedirectResponse
     */
    public function createMonthlyLoan(Request $request): RedirectResponse
    {
        $data = $request->post();

        // validate
        $validator = Validator::make($data, [
            'scheme_id' => 'required|integer|exists:schemes,id',
            'customer_id' => 'required|integer|exists:customers,id',
            'loan_amount' => 'required|numeric',
            'interest_rate' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
        ]);
        if ($validator->fails()) {
            return redirect()->route('customer-loans.monthly')->withErrors($validator)->withInput();
        }

        $interestTotal = ($data['loan_amount'] / 100) * $data['interest_rate'];

        // create loan
        $loan = $this->customerLoanRepository->create([
            'date' => date('Y-m-d'),
            'loan_number' => $this->getNextLoanNumber(),
            'customer_id' => $data['customer_id'],
            'scheme_id' => $data['scheme_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['start_date'],
            'loan_amount' => $data['loan_amount'],
            'interest_rate' => $data['interest_rate'],
            'interest_total' => $interestTotal,
            'no_of_installments' => 0,
            'installment_amount' => 0.00,
            'last_installment_type' => LastInstallmentType::SINGLE,
            'total_arrears' => $data['loan_amount'],
        ]);

        return redirect()->route('customer-loans.monthly')->with('success', 'Monthly loan created successfully.');
    }

    /**
     * Show Bi-Weekly loan create view
     * @return Application|Factory|View
     * @throws InvalidEnumKeyException
     */
    public function biWeeklyLoanView()
    {
        $customerList = $this->customerRepository->getCustomers();
        $scheme = $this->schemeRepository->getByType(SchemeType::fromKey(SchemeType::BI_WEEKLY));
        $holidays = $this->holidayRepository->list();
        return view('customer-loans.types.bi-weekly', compact('customerList', 'scheme', 'holidays'));
    }

        /**
     * Create bi-weekly loan
     * @param Request $request
     * @return RedirectResponse
     */
    public function createBiWeeklyLoan(Request $request): RedirectResponse
    {
        $data = $request->post();

        // validate
        $validator = Validator::make($data, [
            'scheme_id' => 'required|integer|exists:schemes,id',
            'customer_id' => 'required|integer|exists:customers,id',
            'loan_amount' => 'required|numeric',
            'interest_rate' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
        ]);
        if ($validator->fails()) {
            return redirect()->route('customer-loans.monthly')->withErrors($validator)->withInput();
        }

        $interestTotal = ($data['loan_amount'] / 100) * $data['interest_rate'];

        // create loan
        $loan = $this->customerLoanRepository->create([
            'date' => date('Y-m-d'),
            'loan_number' => $this->getNextLoanNumber(),
            'customer_id' => $data['customer_id'],
            'scheme_id' => $data['scheme_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['start_date'],
            'loan_amount' => $data['loan_amount'],
            'interest_rate' => $data['interest_rate'],
            'interest_total' => $interestTotal,
            'no_of_installments' => 0,
            'installment_amount' => 0.00,
            'last_installment_type' => LastInstallmentType::SINGLE,
            'total_arrears' => $data['loan_amount'],
        ]);

        return redirect()->route('customer-loans.bi-weekly')->with('success', 'Bi-weekly loan created successfully.');
    }


}
