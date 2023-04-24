<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    /**
     * @param $data
     * @return mixed
     */
    public function create($data);

    /**
     * @param $id
     * @param $data
     * @return mixed
     */
    public function update($id, $data);

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id);

    /**
     * @return mixed
     */
    public function list();

    /**
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * @param $user
     * @param $role
     * @return mixed
     */
    public function attachRoleToUser($user, $role);
}
