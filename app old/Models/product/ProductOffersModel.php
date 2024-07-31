<?php

namespace App\Models\product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOffersModel extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'product_offers';
    protected $primaryKey = 'offer_id';
    public $timestamps = false;

    public function products()
    {
        return $this->hasMany(ProductModel::class, 'product_id', 'offer_product_id');
    }
}
