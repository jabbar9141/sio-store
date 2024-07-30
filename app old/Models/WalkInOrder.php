<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalkInOrder extends Model
{
    use HasFactory;

    public function items()
    {
        return $this->hasMany(WalkInOrderItem::class, 'walk_in_order_id', 'id');
    }
}
