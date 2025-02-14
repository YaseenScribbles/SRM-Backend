<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRight extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'user_id',
        'create',
        'view',
        'update',
        'delete',
        'print'
    ];

    public $timestamps = false;
}
