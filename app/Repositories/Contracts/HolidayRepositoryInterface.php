<?php

namespace App\Repositories\Contracts;

interface HolidayRepositoryInterface
{
    /**
     * Save holiday
     *
     * @param $data
     * @return mixed
     */
    public function save($data);

    /**
     * Update holiday
     *
     * @param $id
     * @param $data
     * @return mixed
     */
    public function update($id, $data);

    /**
     * Delete holiday
     *
     * @param $id
     * @return mixed
     */
    public function delete($id);

    /**
     * List all holidays
     *
     * @return mixed
     */
    public function list();

    /**
     * Find details of a selected holiday
     *
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * Get holidays for given month and year
     *
     * @param $month
     * @param $year
     * @return mixed
     */
    public function byMonthYear($month, $year);

    /**
     * Get holidays between the given period
     *
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public function betweenPeriod($startDate, $endDate);

}
