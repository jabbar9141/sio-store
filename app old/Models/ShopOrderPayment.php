<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopOrderPayment extends Model
{
    use HasFactory;

    public function order()
    {
        return $this->belongsTo(ShopOrder::class, 'order_id', 'id');
    }
}
