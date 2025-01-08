<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'remarks',
        'user_id'
    ];

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
