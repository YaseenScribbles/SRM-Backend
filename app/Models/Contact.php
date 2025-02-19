<?php

namespace App\Models;

use App\Models\Scopes\UserScope;
use App\Traits\AutoIncrementId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
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
        'pincode',
        'active',
        'user_id',
        'email',
        'distributor_id'
    ];

    public $incrementing = false; // Disable default auto-increment
    protected $keyType = 'int'; // Ensure ID remains an integer

    protected static function booted()
    {
        static::addGlobalScope(new UserScope('c'));
    }
}
