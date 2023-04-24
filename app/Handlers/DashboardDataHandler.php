<?php

namespace App\Handlers;

use App\Repositories\CustomerLoanRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\HolidayRepository;

class DashboardDataHandler
{
    /**
     * @var CustomerLoanRepository
     */
    private $customerLoanRepository;
    /**
     * @var CustomerRepository
     */
    private $customerRepository;
    /**
     * @var HolidayRepository
     */
    private $holidayRepository;

    /**
     * LoanDataHandler constructor.
     * @param CustomerLoanRepository $customerLoanRepository
     * @param CustomerRepository $customerRepository
     * @param HolidayRepository $holidayRepository
     */
    public function __construct(CustomerLoanRepository $customerLoanRepository, CustomerRepository $customerRepository, HolidayRepository $holidayRepository)
    {
        $this->customerLoanRepository = $customerLoanRepository;
        $this->customerRepository = $customerRepository;
        $this->holidayRepository = $holidayRepository;
    }

    public function getSummaryWidgetsData()
    {
        // customers
        $customers = $this->customerRepository->getCustomers();
        // loans
        $loans = $this->customerLoanRepository->getAll();
        // loan installments (total due, total collected)

        // holidays
        // monthly loan summary data for chart (total loans, total collected, total arrears)
    }

    private function getInstallmentsOfLoans($loans)
    {
        $installments = [];
        $loans = $this->customerLoanRepository->getAll();
        foreach ($loans as $loan) {
            // due amount: $loanTotal - $totalPaid

        }
    }

    private function _getLoanData()
    {
        $loans = $this->customerLoanRepository->getAll();
        $totalLoanCount = count($loans);
        foreach ($loans as $loan) {

        }
    }

    private function _getCustomerData(){

    }

}
