<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    const ORDER_CREATED = 0;
    const ORDER_PROCESSING = 1;
    const ORDER_CANCELED = 2;
    const ORDER_ACCEPTED = 3;
    const ORDER_DELIVERED = 4;

    protected $fillable = [
        'product_id', 'user_id', 'quantity', 'address' 
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

}
