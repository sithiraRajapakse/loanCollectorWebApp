<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{

    use SoftDeletes, HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['organization_id', 'name', 'nic_no', 'address', 'telephone', 'email', 'location'];

    /**
     * Customer has many Customer Documents
     *
     * @return HasMany
     */
    public function customerDocuments()
    {
        return $this->hasMany(CustomerDocument::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

}
