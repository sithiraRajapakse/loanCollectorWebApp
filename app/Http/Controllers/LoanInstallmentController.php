<?php

namespace App\Http\Controllers;

use App\Enums\SchemeType;
use App\Repositories\CollectorRepository;
use App\Repositories\HolidayRepository;
use App\Repositories\LoanRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoanInstallmentController extends Controller
{
    /**
     * @var LoanRepository
     */
    private $loanRepository;
    /**
     * @var CollectorRepository
     */
    private $collectorRepository;
    /**
     * @var HolidayRepository
     */
    private $holidayRepository;

    /**
     * LoanInstallmentController constructor.
     * @param LoanRepository $loanRepository
     * @param CollectorRepository $collectorRepository
     * @param HolidayRepository $holidayRepository
     */
    public function __construct(LoanRepository $loanRepository, CollectorRepository $collectorRepository, HolidayRepository $holidayRepository)
    {
        $this->middleware('auth');
        $this->loanRepository = $loanRepository;
        $this->collectorRepository = $collectorRepository;
        $this->holidayRepository = $holidayRepository;
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|Factory|View|RedirectResponse
     */
    public function index(Request $request, $id)
    {
        $loan = $this->loanRepository->getLoanById($id);
        if (empty($loan)) {
            return redirect()->route('loans.register')->with('error', 'Loan not found.');
        }

        $installments = $this->loanRepository->getInstallments($loan->id);
        $holidays = $this->holidayRepository->list();

        $dueDates = $installments->pluck('due_date');
        $holidayDates = $holidays->pluck('date');
        $disablingDates = $dueDates->merge($holidayDates);

        if($loan->scheme->type === SchemeType::MONTHLY) {
            $dueAmount = $this->_calculateDueAmount($loan);
            return view('loans.monthly-installments', compact('loan', 'installments', 'disablingDates', 'dueAmount'));
        } else {
            return view('loans.installments', compact('loan', 'installments', 'disablingDates'));
        }
    }

    private function _calculateDueAmount($loan) {
//        $loan->loanInstallments->
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function listJson(Request $request, $id): JsonResponse
    {
        $loan = $this->loanRepository->getLoanById($id);
        if (empty($loan)) {
            return response()->json(['status' => 'error', 'data' => 'Loan not found.']);
        }

        $loanInstallments = $this->loanRepository->getInstallments($loan->id);
        return response()->json(['status' => 'success', 'data' => $loanInstallments]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addInstallment(Request $request): JsonResponse
    {
        $loanId = $request->post('loan_id');
        $loan = $this->loanRepository->getLoanById($loanId);
        if (empty($loan)) {
            return response()->json(['status' => 'error', 'data' => 'Loan not found.']);
        }

        $validator = Validator::make($request->all(), [
            'loan_id' => 'required|exists:loans,id',
            'installment_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:' . $loan->start_date, 'before_or_equal:' . $loan->end_date],
            'installment_amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'data' => $validator->errors()->toJson()]);
        }

        $indexNumber = $this->loanRepository->getNextInstallmentIndexNo($loan->id);

        $this->loanRepository->createInstallment([
            'index_number' => $indexNumber,
            'loan_id' => $loan->id,
            'installment_amount' => $request->installment_amount,
            'due_date' => $request->installment_date,
            'interest_amount' => $request->installment_amount / 100 * $loan->interest_rate,
        ]);
        return response()->json(['status' => 'success', 'data' => 'Installment added successfully.']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function findInstallment(Request $request): JsonResponse
    {
        $id = $request->post('id');
        $installment = $this->loanRepository->getInstallmentById($id);
        if (empty($installment)) {
            return response()->json(['status' => 'error', 'data' => 'Installment not found.']);
        }
        return response()->json(['status' => 'success', 'data' => $installment]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteInstallment(Request $request): JsonResponse
    {
        $id = $request->post('id');
        $loan = $this->loanRepository->getLoanById($id);
        if (empty($loan)) {
            return response()->json(['status' => 'error', 'data' => 'Loan not found.']);
        }

        // get last installment
        $last = $loan->loanInstallments->last();
        if (empty($last)) {
            return response()->json(['status' => 'error', 'data' => 'No installments found.']);
        }

        if (!empty($last->paid_at)) {
            return response()->json(['status' => 'error', 'data' => 'Last installment has been already paid. Deletion is denied.']);
        }

        $this->loanRepository->deleteInstallment($last->id);
        return response()->json(['status' => 'success', 'data' => "Installment deleted successfully."]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getFirstPayableInstallment(Request $request): JsonResponse
    {
        $loanId = $request->get('loan_id');
        $loan = $this->loanRepository->getLoanById($loanId);
        if (empty($loan)) {
            return response()->json(['status' => 'error', 'data' => 'Loan not found.']);
        }

        if (empty($loan->loanInstallments)) {
            return response()->json(['status' => 'error', 'data' => 'No installments added to the loan. Please add installments and try again.']);
        }

        $payableInstallment = $this->loanRepository->getLeadingPayableInstallment($loan->id);
        if (empty($payableInstallment)) {
            return response()->json(['status' => 'error', 'data' => 'No last payable installment found.']);
        }
        return response()->json(['status' => 'success', 'data' => $payableInstallment]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function receivePayment(Request $request): JsonResponse
    {
        $installmentId = $request->post('installment_id');
        $paid_amount = $request->post('paid_amount');

        $loanInstallment = $this->loanRepository->getInstallmentById($installmentId);
        if (empty($loanInstallment)) {
            return response()->json(['status' => 'error', 'data' => 'Installment you are trying to make the payment is not available.']);
        }

        if ($loanInstallment->paid_at != null) {
            return response()->json(['status' => 'error', 'data' => 'Payment already made for selected installment.']);
        }

        // make payment
        $arrearsAmount = doubleval($loanInstallment->installment_amount) - doubleval($paid_amount);

        $user = auth()->user();
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
     * @param Request $request
     * @return JsonResponse
     */
    public function getPayableInstallmentByIdJSON(Request $request): JsonResponse
    {
        $installmentId = $request->post('installment_id');
        $loanInstallment = $this->loanRepository->getInstallmentById($installmentId);

        // get arrears total for the loan
        $arrearsTotal = $loanInstallment->loan->arrearsTotal();

        if (empty($loanInstallment)) {
            return response()->json(['status' => 'error', 'data' => 'Installment you are trying to make the payment is not available.']);
        }

        return response()->json(['status' => 'success', 'data' => ['installment' => $loanInstallment, 'arrears_total' => $arrearsTotal]]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function reverseInstallmentPayment(Request $request): JsonResponse
    {
//        $installmentId = $this->post('installment_id');
//
//        $installment = $this->loanRepository->getInstallmentById($installmentId);
//
//        // if installment is not found
//        if (empty($installment)) {
//            return response()->json(['status' => 'error', 'data' => 'Installment you are trying to make the payment is not available.']);
//        }
//
//        // not allowed for not paid installment
//        if (empty($installment->paid_amount)) {
//            return response()->json(['status' => 'error', 'data' => 'Installment you are trying to make the payment is not available.']);
//        }
//
//        $loan = $installment->loan;
//        // deduct the paid amount from the loan amount
////        $loan->
//
//        // deduct the payment to the arrears
//        $installment->paid_amount = 0.00;
//        $installment->paid_at = null;
//        $installment->arrears_amount = 0.00;

        // set payment amount to zero
        return response()->json(['status' => 'error', 'data' => 'This feature is yet to be made available.']);
    }

    public function createMonthlyLoanPayment(Request $request, $id)
    {
        $loan = $this->loanRepository->getLoanById($id);
        if (empty($loan)) {
            return redirect()->route('customer-loans.list')->with('error', 'Loan not found. Please try again with a different loan entry.');
        }

        $data = $request->post();
        $validator = Validator::make($data, [
            "payment_date" => "required|date",
            "installment_amount" => "required|numeric",
        ]);
        if ($validator->fails()) {
            return redirect()->route('loan.installments.edit', $loan->id)->with('error', 'You have errors in your input. Please correct them and try submitting again.')->withErrors($validator)->withInput();
        }

        // calculate
        $interestAmount = ($loan->loan_amount / 100) * $loan->interest_rate;
        $amountDeductable = $data['installment_amount'] - $interestAmount;
        $arrearsAmount = $amountDeductable < 0 ? $amountDeductable : 0.0;

        // get collector
        $user = auth()->user();
        $collector = $this->collectorRepository->getCollectorByUserId($user->id);

        // create installment
        $indexNumber = $this->loanRepository->getNextInstallmentIndexNo($loan->id);
        $this->loanRepository->createInstallment([
            'loan_id' => $loan->id,
            'index_number' => $indexNumber,
            'due_date' => date('Y-m-d H:i:s', strtotime($data['payment_date'])),
            'paid_amount' => doubleval($data['installment_amount']),
            'paid_at' => date('Y-m-d H:i:s'),
            'collector_id' => $collector->id,
            'interest_amount' => $interestAmount,
            'installment_amount' => $amountDeductable > 0 ? $amountDeductable : 0,
            'arrears_amount' => $arrearsAmount,
        ]);

        // TODO: make adjustment to overall arrears amount
        return redirect()->route('loan.installments.edit', $loan->id)->with('success', 'Installment payment added successfully.');
    }

}
