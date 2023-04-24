<?php


namespace App\Repositories\Contracts;

use App\Enums\SchemeType;
use App\Models\Scheme;
use Illuminate\Database\Eloquent\Collection;

interface SchemeRepositoryInterface
{

    /**
     * Create new Scheme entry
     *
     * @param $data
     * @return Scheme
     */
    public function create($data): Scheme;

    /**
     * Get all Scheme records ordered by title
     *
     * @return Collection|null
     */
    public function getAll(): ?Collection;

    /**
     * Get the Scheme entry identified by the id value
     *
     * @param $id
     * @return Scheme|null
     */
    public function getById($id): ?Scheme;

    /**
     * Update the Scheme entry identified by the id value
     * with the data set
     *
     * @param $id
     * @param $data
     * @return bool|null
     */
    public function update($id, $data): ?bool;

    /**
     * Delete the Scheme entry
     * identified by the id value
     *
     * @param $id
     * @return bool|null
     */
    public function delete($id): ?bool;

    /**
     * Get the loan scheme by the scheme type
     * @param SchemeType $schemeType
     * @return mixed
     */
    public function getByType(SchemeType $schemeType);

}
