<?php

namespace App\Repositories\Contracts;

interface CustomerRepositoryInterface
{
    /**
     * Create new customer entry
     *
     * @param array $data
     * @return mixed
     */
    public function createCustomer(array $data);

    /**
     * Get a collection of customers
     *
     * @return mixed
     */
    public function getCustomers();

    /**
     * Get customer by id
     *
     * @param int $id
     * @return mixed
     */
    public function getCustomerById(int $id);

    /**
     * Update the customer entry
     *
     * @param $id
     * @param $data
     * @return mixed
     */
    public function updateCustomer($id, $data);

    /**
     * Delete customer entry
     *
     * @param $id
     * @return mixed
     */
    public function deleteCustomer($id);

    /**
     * Get customers by organization
     *
     * @param $organizationId
     * @return mixed
     */
    public function getCustomersByOrganizationId($organizationId);

    /**
     * Create customer document entry
     *
     * @param $customerId
     * @param array $documentData
     * @return mixed
     */
    public function createCustomerDocument($customerId, array $documentData);

    /**
     * Get collection of ducoments for the customer
     *
     * @param $customerId
     * @return mixed
     */
    public function getCustomerDocuments($customerId);

    /**
     * Get customer document by id
     *
     * @param $documentId
     * @return mixed
     */
    public function getCustomerDocumentById($documentId);

    /**
     * Set the document to locked mode
     *
     * @param $documentid
     * @return mixed
     */
    public function lockDocument($documentid);

    /**
     * Set the document to unlocked mode
     *
     * @param $documentId
     * @return mixed
     */
    public function unlockDocument($documentId);

    /**
     * Delete the document entry
     *
     * @param $documentId
     * @return mixed
     */
    public function deleteDocument($documentId);

    /**
     * Delete the customer documents by customer id
     *
     * @param $customerId
     * @return mixed
     */
    public function deleteDocumentsByCustomerId($customerId);

}
