<?php

namespace App\Models;

use App\Models\product\ProductModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalkInOrderItem extends Model
{
    use HasFactory;

    public function order()
    {
        return $this->belongsTo(WalkInOrder::class, 'walk_in_order_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id', 'product_id');
    }

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }
}
