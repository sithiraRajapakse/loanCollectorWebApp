<?php

namespace App\Repositories;

use App\Models\Organization;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function create($data)
    {
        $organization = Organization::first();
        $data['organization_id'] = $organization->id;
        return User::create($data);
    }

    /**
     * @inheritDoc
     */
    public function update($id, $data)
    {
        $user = User::where('id', $id)->first();
        return $user->update($data);
    }

    /**
     * @inheritDoc
     */
    public function delete($id)
    {
        $user = User::where('id', $id)->first();
        return $user->delete();
    }

    /**
     * @inheritDoc
     */
    public function list()
    {
        return User::orderBy('name')->get();
    }

    /**
     * @inheritDoc
     */
    public function find($id)
    {
        return User::where('id', $id)->first();
    }

    /**
     * @inheritDoc
     */
    public function attachRoleToUser($user, $role)
    {
        $user->syncRoles([$role]);
    }
}
