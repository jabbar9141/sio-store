<?php

namespace App\Models;

use App\Models\product\ProductModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandModel extends Model
{
    use HasFactory;
    protected $table = 'brand';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'brand_id';

    protected $fillable = [
        'brand_name',
        'brand_image',
        'brand_slug'
    ];

    public function products()
    {
        return $this->hasMany(ProductModel::class, 'brand_id', 'brand_id');
    }
}
