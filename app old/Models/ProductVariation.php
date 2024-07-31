<?php

namespace App\Models;

use App\Models\product\ProductModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $fillable = [
        'width',
        'height',
        'length',
        'weight',
        'product_id',
        'color_id',
        'size_id',
        'dimention_id',
        'image_url',
        'price',
        'product_quantity',
        'color_name',
        'size_name',
        'whole_sale_price',
    ];

    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id', 'product_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }
}
