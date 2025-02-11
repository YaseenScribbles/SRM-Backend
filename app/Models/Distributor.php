<?php

namespace App\Models;

use App\Traits\AutoIncrementId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    use HasFactory;
    use AutoIncrementId;

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

    public $incrementing = false; // Disable default auto-increment
    protected $keyType = 'int'; // Ensure ID remains an integer
}
