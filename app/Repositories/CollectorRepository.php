<?php


namespace App\Repositories;

use App\Models\Collector;
use App\Models\LoanInstallment;
use App\Repositories\Contracts\CollectorRepositoryInterface;
use App\Models\User;

class CollectorRepository implements CollectorRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function create($data)
    {
        $user = User::create($data['user']);

        // set user id
        $data['collector']['user_id'] = $user->id;
        return Collector::create($data['collector']);
    }

    /**
     * @inheritDoc
     */
    public function getAll()
    {
        return Collector::orderBy('name')->get();
    }

    /**
     * @inheritDoc
     */
    public function update($id, $data)
    {
        return Collector::where('id', $id)->update($data);
    }

    /**
     * @inheritDoc
     */
    public function updatePassword($id, $data)
    {
        $collector = $this->getById($id);
        $collector->user->update([
            'password' => $data['password'],
        ]);
        return $collector;
    }

    /**
     * @inheritDoc
     */
    public function getById($id)
    {
        return Collector::where('id', $id)->first();
    }

    /**
     * @inheritDoc
     */
    public function delete($id)
    {
        return Collector::where('id', $id)->delete();
    }

    /**
     * @inheritDoc
     */
    public function getCollectorByUserId($id): ?Collector
    {
        return Collector::where('user_id', $id)->first();
    }
}
