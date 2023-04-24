<?php

namespace App\Http\Controllers;

use App\Repositories\CustomerRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * CustomerController constructor.
     * @param CustomerRepository $customerRepository
     */
    public function __construct(CustomerRepository $customerRepository)
    {
        $this->middleware('auth');
        $this->customerRepository = $customerRepository;
    }

    /**
     * Shows the customer list view
     */
    public function index()
    {
        $customers = $this->customerRepository->getCustomers();
        return view('customer.list', compact('customers'));
    }

    /**
     * Shows the customer registration view
     */
    public function register()
    {
        return view('customer.create');
    }

    /**
     * Process customer registration request
     *
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function processRegistration(Request $request)
    {
        $requestData = $request->all();

        $validator = Validator::make($requestData, [
            'customer_name' => 'required',
            'nic_no' => ['nullable', 'regex:/^([1-9]{1}[0-9]{8}[vVxX])|([1-2]{1}[0-9]{11})$/'],
            'telephone' => ['nullable', 'regex:/^0[1-9]{1}[0-9]{8}$/'],
            'email_address' => 'nullable|email',
            'address' => 'nullable',
            'location' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->route('customers.register')->withErrors($validator)->withInput();
        }

        $this->customerRepository->createCustomer($requestData);

        return redirect()->route('customers')->with('success', 'Customer created successfully.');
    }

    /**
     * Show customer edit screen
     *
     * @param $id
     */
    public function edit($id)
    {
        $customer = $this->customerRepository->getCustomerById($id);
        if (empty($customer))
            return redirect()->route('customers')->with('error', 'Customer not found.');

        $customerDocuments = $this->customerRepository->getCustomerDocuments($customer->id);

        return view('customer.edit', compact('customer', 'customerDocuments'));
    }

    /**
     * Process customer details update
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function processUpdate(Request $request, $id)
    {
        $requestData = $request->all();

        $validator = Validator::make($requestData, [
            'customer_name' => 'required',
            'nic_no' => ['nullable', 'regex:/^([1-9]{1}[0-9]{8}[vVxX])|([1-2]{1}[0-9]{11})$/'],
            'telephone' => ['nullable', 'regex:/^0[1-9]{1}[0-9]{8}$/'],
            'email_address' => 'nullable|email',
            'address' => 'nullable',
            'location' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->route('customers.edit', $id)->withErrors($validator)->withInput();
        }

        $this->customerRepository->updateCustomer($id, $requestData);

        return redirect()->route('customers')->with('success', 'Customer details updated successfully.');
    }

    /**
     * Process deletion of a customer record
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function processDelete(Request $request, $id)
    {
        $customer = $this->customerRepository->getCustomerById($id);
        if (empty($customer)) {
            return redirect()->route('customers')->with('error', 'Customer not found.');
        }

        $folder = storage_path('app/customerfiles/customer_' . $customer->id);
        File::deleteDirectory($folder); // delete the whole directory

        $this->customerRepository->deleteCustomer($id);

        return redirect()->route('customers')->with('success', 'Customer deleted successfully.');
    }

    /**
     * Process customer document upload
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function processDocumentUpload(Request $request, $id)
    {
        // validate if valid customer
        $customer = $this->customerRepository->getCustomerById($id);
        if (empty($customer)) {
            return redirect()->route('customers')->with('error', 'Customer not found.');
        }

        $file = $request->file('uploadfile');
        $path = $file->store('customer_documents/customer_' . $id);
        if ($path) {
            $document = [
                'name' => $request->title,
                'file' => $path
            ];
            // file uploaded successfully
            $this->customerRepository->createCustomerDocument($id, $document);
            return redirect()->route('customers.edit', $customer->id)->with('success', 'Document uploaded and saved successfully.');
        }
        return redirect()->route('customers.edit', $customer->id)->with('error', 'Failed to upload document file.');
    }

    /**
     * Lock or unlock the document entry
     * identified by the id
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function processLockingDocument(Request $request, $id)
    {
        $customerId = $request->customer_id;

        $customer = $this->customerRepository->getCustomerById($customerId);
        if (empty($customer)) {
            return redirect()->route('customers')->with('error', 'Failed to find the customer.');
        }

        $doc = $this->customerRepository->getCustomerDocumentById($id);
        if (empty($doc)) {
            return redirect()->route('customers.edit', $customerId)->with('error', 'Failed to find the document.');
        }

        // set locked flag to the document entry
        $this->customerRepository->lockDocument($doc->id);

        return redirect()->route('customers.edit', $customerId)->with('success', 'Document locked successfully.');
    }

    public function processUnlockingDocument(Request $request, $id)
    {
        $customerId = $request->customer_id;

        $customer = $this->customerRepository->getCustomerById($customerId);
        if (empty($customer)) {
            return redirect()->route('customers')->with('error', 'Failed to find the customer.');
        }

        $doc = $this->customerRepository->getCustomerDocumentById($id);
        if (empty($doc)) {
            return redirect()->route('customers.edit', $customerId)->with('error', 'Failed to find the document.');
        }

        // set locked flag to the document entry
        $this->customerRepository->unlockDocument($doc->id);

        return redirect()->route('customers.edit', $customerId)->with('success', 'Document unlocked successfully.');
    }

    public function processDeleteDocument(Request $request, $id)
    {
        $customerId = $request->customer_id;

        $customer = $this->customerRepository->getCustomerById($customerId);
        if (empty($customer)) {
            return redirect()->route('customers')->with('error', 'Customer not found.');
        }

        $doc = $this->customerRepository->getCustomerDocumentById($id);
        if (empty($doc)) {
            return redirect()->route('customers.edit', $customerId)->with('error', 'Failed to upload document file.');
        }

        if ($doc->is_locked) {
            return redirect()->route('customers.edit', $customerId)->with('error', 'Cannot allow to delete a locked document.');
        }

        $path = storage_path('app/' . $doc->file);
        if (File::delete($path)) {
            // delete database record if file deleted.
            $this->customerRepository->deleteDocument($id);
        }

        return redirect()->route('customers.edit', $customerId)->with('success', 'Document deleted successfully.');
    }

    public function getCustomersListJSON(Request $request)
    {
        $customers = $this->customerRepository->getCustomers();
        return response()->json($customers);
    }

    public function getCustomerJSON(Request $request)
    {
        $customerId = $request->get('id');
        $customer = $this->customerRepository->getCustomerById($customerId);
        return response()->json($customer);
    }

}
