<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerDocument extends Model
{
    use SoftDeletes;

    protected $fillable = ['customer_id', 'name', 'file', 'new_document_id', 'is_locked'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
