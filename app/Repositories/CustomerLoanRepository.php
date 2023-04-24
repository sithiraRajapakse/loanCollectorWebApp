<?php
namespace App\Repositories;

use App\Enums\LastInstallmentType;
use App\Enums\SchemeType;
use App\Models\Loan;

class CustomerLoanRepository implements Contracts\CustomerLoanRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function create($data)
    {
        return Loan::create($data);
    }

    /**
     * @inheritDoc
     */
    public function createInstallments($loanId, $installments)
    {
        $loan = Loan::whereId($loanId)->first();
        return $loan->loanInstallments()->createMany($installments);
    }

    /**
     * @inheritDoc
     */
     public function getLatestLoanNumberByYear($year)
    {
//        return Loan::selectRaw('MAX(CAST(`loan_number` AS INTEGER))')->whereYear('date', $year)->first();
         return Loan::selectRaw('MAX(CAST(`loan_number` AS INTEGER)) AS loan_number')->whereYear('date', $year)->first();;
    }

    /**
     * @inheritDoc
     */
    public function getAll()
    {
        return Loan::orderBy('loan_number', 'asc')->get();
    }

    /**
     * @inheritDoc
     */
    public function getAllByType($schemeId)
    {
        return Loan::where('scheme_id', $schemeId)->orderBy('loan_number', 'asc')->get();
    }

    /**
     * @inheritDoc
     */
    public function getTotalArrearsForLoan($loanId)
    {
        $loan = Loan::whereId($loanId)->first();
        return $loan->loanInstallments->sum('arrears_amount');
    }

    /**
     * @inheritDoc
     */
    public function getLoansByCustomerId($customerId)
    {
        return Loan::where('customer_id', $customerId)
            ->with(['scheme', 'customer'])
            ->get();
    }
}
