<?php

namespace App\Models;

use App\Traits\AutoIncrementId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;
    use AutoIncrementId;

    protected $fillable = [
        'contact_id',
        'purpose_id',
        'description',
        'response',
        'user_id'
    ];

    public function visit_images()
    {
        return $this->hasMany(VisitImage::class)->select('visit_id','image_path');
    }

    public $incrementing = false; // Disable default auto-increment
    protected $keyType = 'int'; // Ensure ID remains an integer
}
