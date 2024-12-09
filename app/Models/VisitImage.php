<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'image_path',
        'user_id'
    ];
}
