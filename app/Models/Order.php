<?php

namespace App\Models;

use App\Models\Scopes\UserScope;
use App\Traits\AutoIncrementId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    use AutoIncrementId;

    protected $fillable = [
        'contact_id',
        'remarks',
        'user_id'
    ];

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public $incrementing = false; // Disable default auto-increment
    protected $keyType = 'int'; // Ensure ID remains an integer

    protected static function booted()
    {
        static::addGlobalScope(new UserScope('o'));
    }

}
