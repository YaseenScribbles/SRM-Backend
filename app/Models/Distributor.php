<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'district',
        'state_id',
        'phone',
        'email',
        'pincode',
        'active',
        'user_id',
    ];

}
