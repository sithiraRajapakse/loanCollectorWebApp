<?php


namespace App\Repositories;

use App\Enums\SchemeType;
use App\Models\Scheme;
use App\Repositories\Contracts\SchemeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SchemeRepository implements SchemeRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function create($data): Scheme
    {
        return Scheme::create($data);
    }

    /**
     * @inheritDoc
     */
    public function getAll(): ?Collection
    {
        return Scheme::all();
    }

    /**
     * @inheritDoc
     */
    public function getById($id): ?Scheme
    {
        return Scheme::where('id', $id)->first();
    }

    /**
     * @inheritDoc
     */
    public function update($id, $data): ?bool
    {
        $scheme = Scheme::where('id', $id)->first();
        return $scheme->update($data);
    }

    /**
     * @inheritDoc
     */
    public function delete($id): ?bool
    {
        return Scheme::where('id', $id)->delete();
    }

    /**
     * @inheritDoc
     */
    public function getByType(SchemeType $schemeType)
    {
        return Scheme::whereType($schemeType)->first();
    }
}
