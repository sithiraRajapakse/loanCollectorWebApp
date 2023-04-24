<?php

namespace App\Repositories;

use App\Models\Holiday;
use App\Repositories\Contracts\End;
use App\Repositories\Contracts\Start;

class HolidayRepository implements Contracts\HolidayRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function save($data)
    {
        return Holiday::create($data);
    }

    /**
     * @inheritDoc
     */
    public function update($id, $data)
    {
        return Holiday::where('id', $id)->update($data);
    }

    /**
     * @inheritDoc
     */
    public function delete($id)
    {
        return Holiday::where('id', $id)->delete();
    }

    /**
     * @inheritDoc
     */
    public function list()
    {
        return Holiday::orderBy('date', 'asc')->orderBy('title', 'asc')->get();
    }

    /**
     * @inheritDoc
     */
    public function find($id)
    {
        return Holiday::where('id', $id)->first();
    }

    /**
     * @inheritDoc
     */
    public function byMonthYear($month, $year)
    {
        return Holiday::whereMonth('date', $month)->whereYear('date', $year)->orderby('date', 'asc')->get();
    }

    /**
     * @inheritDoc
     */
    public function betweenPeriod($startDate, $endDate)
    {
        return Holiday::whereBetween('date', [$startDate, $endDate])->get();
    }
}
