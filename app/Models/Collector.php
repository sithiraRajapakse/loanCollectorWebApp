<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collector extends Model
{

    use SoftDeletes;

    protected $fillable = ['user_id', 'name', 'address', 'telephone', 'nic_no', 'drivers_license_no'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
