<?php

namespace App\Repositories;

use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Repositories\Contracts\LoanRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LoanRepository implements LoanRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function registerLoan($data): ?Loan
    {
        return Loan::create($data);
    }

    /**
     * @inheritDoc
     */
    public function getLatestLoanNumberByYear($year): ?string
    {
        return Loan::whereYear('date', $year)->max('loan_number');
    }

    /**
     * @inheritDoc
     */
    public function getLoanById($id): ?Loan
    {
        return Loan::where('id', $id)->first();
    }

    /**
     * @inheritDoc
     */
    public function registerInstallmentsForLoan($loan, $installments)
    {
        return DB::transaction(function () use ($loan, $installments) {
            foreach ($installments as $installment) {
                LoanInstallment::create([
                    'loan_id' => $loan->id,
                    'index_number' => $installment['number'],
                    'due_date' => $installment['dueDate'],
                    'installment_amount' => $installment['amount'],
                    'interest_amount' => (doubleval($installment['amount']) / 100 * $loan->interest_rate),
                ]);
            }
        });
    }

    /**
     * @inheritDoc
     */
    public function getAllLoans(): Collection
    {
        return Loan::orderBy('loan_number', 'desc')->get();
    }

    /**
     * @inheritDoc
     */
    public function getInstallments($loanId): ?Collection
    {
        return LoanInstallment::where('loan_id', $loanId)->orderBy('due_date', 'asc')->get();
    }

    /**
     * @inheritDoc
     */
    public function getInstallmentById($id): ?LoanInstallment
    {
        return LoanInstallment::where('id', $id)->first();
    }

    /**
     * @inheritDoc
     */
    public function updateInstallment($id, $data): ?int
    {
        return LoanInstallment::where('id', $id)->update($data);
    }

    /**
     * @inheritDoc
     */
    public function deleteInstallment($id)
    {
        $loanInstallment = LoanInstallment::where('id', $id)->first();
        return $loanInstallment->delete();
    }

    /**
     * @inheritDoc
     */
    public function createInstallment($data): ?LoanInstallment
    {
        return LoanInstallment::create($data);
    }

    /**
     * @inheritDoc
     */
    public function getNextInstallmentIndexNo($loanId): int
    {
        $indexNo = LoanInstallment::where('loan_id', $loanId)->max('index_number');
        if (empty($indexNo)) {
            return 1;
        }
        return intval($indexNo) + 1;
    }

    /**
     * @inheritDoc
     */
    public function getLeadingPayableInstallment($loanId): ?LoanInstallment
    {
        return LoanInstallment::where('loan_id', $loanId)
            ->whereNull('paid_at')
            ->orderBy('due_date', 'asc')
            ->first();
    }

    /**
     * @inheritDoc
     */
    public function getPayableAmountForTodayByCustomerId($customerId)
    {
        return LoanInstallment::join('loans', 'loans.id', '=', 'loan_installments.loan_id')
            ->join('customers', 'customers.id', '=', 'loans.customer_id')
            ->where('customers.id', $customerId)
            ->whereRaw('loan_installments.due_date = CURRENT_DATE')
            ->select('loan_installments.*')
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getArrearsInstallments($loanId)
    {
        return LoanInstallment::where('loan_id', $loanId)
            ->where('arrears_amount', '>', 0)
            ->whereNull('arrears_settled_at')
            ->orderBy('index_number', 'asc');
    }
}
