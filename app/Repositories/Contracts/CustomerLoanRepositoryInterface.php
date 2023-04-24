<?php


namespace App\Repositories\Contracts;


use App\Enums\SchemeType;
use App\Models\Loan;

interface CustomerLoanRepositoryInterface
{
    /**
     * Register the loan using the given data
     *
     * @param $data
     * @return mixed
     */
    public function create($data);

    /**
     * Register installments for the loan identified by the id given
     *
     * @param $loanId
     * @param $installments
     * @return mixed
     */
    public function createInstallments($loanId, $installments);

    /**
     * @param $year
     * @return mixed
     */
    public function getLatestLoanNumberByYear($year);

    /**
     * Get all loans registered in the system
     * @return mixed
     */
    public function getAll();

    /**
     * Get all loans belonging to a category
     *
     * @param $schemeId
     * @return mixed
     */
    public function getAllByType($schemeId);

    /**
     * Get the total arrears for a loan identified by the ID
     *
     * @param $loanId
     * @return mixed
     */
    public function getTotalArrearsForLoan($loanId);

    /**
     * Get loans by customer id
     *
     * @param $customerId
     * @return mixed
     */
    public function getLoansByCustomerId($customerId);
}
