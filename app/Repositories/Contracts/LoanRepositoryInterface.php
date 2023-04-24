<?php

namespace App\Repositories\Contracts;

use App\Models\Loan;
use App\Models\LoanInstallment;
use Illuminate\Database\Eloquent\Collection;

interface LoanRepositoryInterface
{

    /**
     * Create new loan entry in the database
     *
     * @param $data
     * @return Loan|null
     */
    public function registerLoan($data): ?Loan;

    /**
     * Get the last generated loan number for the given year
     * Returns null if no entries are found.
     *
     * @param $year
     * @return string|null
     */
    public function getLatestLoanNumberByYear($year): ?string;

    /**
     * Get loan entry by id
     *
     * @param $id
     * @return Loan|null
     */
    public function getLoanById($id): ?Loan;

    /**
     * Register loan installments for the given loan
     *
     * @param $loan
     * @param $installments
     * @return mixed
     */
    public function registerInstallmentsForLoan($loan, $installments);

    /**
     * Get All Loans
     *
     * @return Collection|null
     */
    public function getAllLoans(): ?Collection;

    /**
     * Get installments list for the loan
     * identified by the given loan id
     *
     * @param $loanId
     * @return Collection|null
     */
    public function getInstallments($loanId): ?Collection;

    /**
     * Get installment entry identified by the id
     *
     * @param $id
     * @return LoanInstallment|null
     */
    public function getInstallmentById($id): ?LoanInstallment;

    /**
     * Update installment entry
     * identified by the id value
     * with the values in the given data array
     *
     * @param $id LoanInstallment installment ID
     * @param $data array New data array
     * @return int|null
     */
    public function updateInstallment($id, $data): ?int;

    /**
     * Delete last installment of the loan identified by the given id
     *
     * @param $id
     * @return mixed
     */
    public function deleteInstallment($id);

    /**
     * Create new loan installment entry
     *
     * @param $data
     * @return LoanInstallment|null
     */
    public function createInstallment($data): ?LoanInstallment;

    /**
     * Get the next installment index no
     *
     * @param $loanId
     * @return int
     */
    public function getNextInstallmentIndexNo($loanId): int;

    /**
     * Get the first installment to pay
     * for the loan identified by the loan id
     *
     * @param $loanId
     * @return LoanInstallment|null
     */
    public function getLeadingPayableInstallment($loanId): ?LoanInstallment;

    /**
     * Get payable amount for today by customer id
     *
     * @param $customerId
     * @return mixed
     */
    public function getPayableAmountForTodayByCustomerId($customerId);

    /**
     * Get arrears installments for the loan identified by the id
     *
     * @param $loanId
     * @return mixed
     */
    public function getArrearsInstallments($loanId);

}
