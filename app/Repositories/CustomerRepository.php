<?php

namespace App\Repositories;

use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Models\CustomerDocument;
use App\Models\Customer;

class CustomerRepository implements CustomerRepositoryInterface {

    /**
     * @inheritDoc
     */
    public function createCustomer(array $data)
    {
        $customer = new Customer;
        $customer->organization_id = 1;
        $customer->name = $data['customer_name'];
        $customer->nic_no = $data['nic_no'];
        $customer->telephone = $data['telephone'];
        $customer->email = $data['email_address'];
        $customer->address = $data['address'];
        $customer->location = $data['location'];
        $customer->save();
    }

    /**
     * @inheritDoc
     */
    public function getCustomers()
    {
        return Customer::orderBy('name')->get();
    }

    /**
     * @inheritDoc
     */
    public function getCustomerById(int $id)
    {
        return Customer::where('id', $id)->first();
    }

    /**
     * @inheritDoc
     */
    public function updateCustomer($id, $data)
    {
        $customer = Customer::where('id', $id)->first();
        $customer->name = $data['customer_name'];
        $customer->nic_no = $data['nic_no'];
        $customer->telephone = $data['telephone'];
        $customer->email = $data['email_address'];
        $customer->address = $data['address'];
        $customer->location = $data['location'];
        $customer->save();
    }

    /**
     * @inheritDoc
     */
    public function deleteCustomer($id)
    {
        $customer = Customer::where('id', $id)->first();
        $customer->delete();
    }

    /**
     * @inheritDoc
     */
    public function getCustomersByOrganizationId($organizationId)
    {
        return Customer::where('organization_id', $organizationId)->get();
    }

    /**
     * @inheritDoc
     */
    public function createCustomerDocument($customerId, array $documentData)
    {
        $document = new CustomerDocument;
        $document->customer_id = $customerId;
        $document->name = $documentData['name'];
        $document->file = $documentData['file'];
        $document->save();
    }

    /**
     * @inheritDoc
     */
    public function getCustomerDocuments($customerId)
    {
        $customer = Customer::where('id', $customerId)->first();
        if ($customer) {
            return $customer->customerDocuments;
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getCustomerDocumentById($documentId)
    {
        return CustomerDocument::where('id', $documentId)->first();
    }

    /**
     * @inheritDoc
     */
    public function lockDocument($documentId)
    {
        $document = $this->getCustomerDocumentById($documentId);
        $document->is_locked = true;
        $document->save();
    }

    /**
     * @inheritDoc
     */
    public function unlockDocument($documentId)
    {
        $document = $this->getCustomerDocumentById($documentId);
        $document->is_locked = false;
        $document->save();
    }

    /**
     * @inheritDoc
     */
    public function deleteDocument($documentId)
    {
        $this->getCustomerDocumentById($documentId)->delete();
    }

    /**
     * @inheritDoc
     */
    public function deleteDocumentsByCustomerId($customerId)
    {
        CustomerDocument::where('customer_id', $customerId)->delete();
    }

}
