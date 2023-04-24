<?php

namespace App\Repositories\Contracts;

use App\Models\Collector;

interface CollectorRepositoryInterface
{
    /**
     * Create new Collector record in database
     *
     * @param $data
     * @return mixed
     */
    public function create($data);

    /**
     * Get the Collector record identified by the id value
     *
     * @param $id
     * @return mixed
     */
    public function getById($id);

    /**
     * Get all Collector records from the table
     *
     * @return mixed
     */
    public function getAll();

    /**
     * Update Collector record identified by the id value
     * with the given data
     *
     * @param $id
     * @param $data
     * @return mixed
     */
    public function update($id, $data);

    /**
     * Update the user account data of the collector record
     * identified by the collector id
     *
     * @param $id
     * @param $data
     * @return mixed
     */
    public function updatePassword($id, $data);

    /**
     * Delete the Collector record identified by the id value
     *
     * @param $id
     * @return mixed
     */
    public function delete($id);

    /**
     * Get collector by user id
     *
     * @param $id
     * @return Collector|null
     */
    public function getCollectorByUserId($id): ?Collector;

}
