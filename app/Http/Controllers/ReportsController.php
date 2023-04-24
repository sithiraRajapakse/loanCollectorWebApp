<?php

namespace App\Http\Controllers;

use App\Enums\ReportPrintType;
use App\Repositories\CustomerLoanRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\SchemeRepository;
use Facade\FlareClient\Report;
use Illuminate\Http\Request;

class ReportsController extends Controller
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
     * @var SchemeRepository
     */
    private $schemeRepository;

    /**
     * ReportsController constructor.
     * @param CustomerRepository $customerRepository
     * @param CustomerLoanRepository $customerLoanRepository
     * @param SchemeRepository $schemeRepository
     */
    public function __construct(CustomerRepository $customerRepository, CustomerLoanRepository $customerLoanRepository, SchemeRepository $schemeRepository)
    {
        $this->middleware('auth');
        $this->customerRepository = $customerRepository;
        $this->customerLoanRepository = $customerLoanRepository;
        $this->schemeRepository = $schemeRepository;
    }

    public function index()
    {
        // loan schemes
        $schemes = $this->schemeRepository->getAll();
        return view('reports.index', compact('schemes'));
    }

    public function customers($type)
    {
        $customers = $this->customerRepository->getCustomers();
        if ($type == ReportPrintType::PRINT) {
            return view('reports.customers.customers-report-print', compact('customers'));
        } else {
            return abort(503, 'Invalid request.');
        }
    }

    public function loanList($type)
    {
        $loans = $this->customerLoanRepository->getAll();
        if($type == ReportPrintType::PRINT) {
            return view('reports.loans.loans-list-report-print', compact('loans'));
        } else {
            return abort(503, 'Invalid request.');
        }
    }

    public function loanListByScheme(Request $request, $type) {
        $schemeId = $request->post('scheme_id');
        $scheme = $this->schemeRepository->getById($schemeId);
        if (empty($scheme)) {
            return abort(503, 'Invalid request. Cannot identify scheme.');
        }

        $loans = $this->customerLoanRepository->getAllByType($scheme->id);
        if($type == ReportPrintType::PRINT) {
            return view('reports.loans.loans-list-scheme-report-print', compact('loans', 'scheme'));
        } else {
            return abort(503, 'Invalid request.');
        }
    }

    public function collections($type)
    {

    }

}
