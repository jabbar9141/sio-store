<?php

namespace App\Models\product;

use App\Models\BrandModel;
use App\Models\CategoryModel;
use App\Models\Location;
use App\Models\ProductReview;
use App\Models\SubCategoryModel;
use App\Models\ProductVariation;
use App\Models\User;
use App\Models\VendorShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    use HasFactory;
    protected $table = 'product';
    protected $primaryKey = 'product_id';
    protected $guarded = [];


    public function images()
    {
        return $this->hasMany(ProductImagesModel::class, 'image_product_id', 'product_id');
    }

    public function sub_category()
    {
        return $this->belongsTo(SubCategoryModel::class, 'sub_category_id', 'sub_category_id');
    }

    public function category()
    {
        return $this->belongsTo(CategoryModel::class, 'category_id', 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(BrandModel::class, 'brand_id', 'brand_id');
    }

    public function vendor()
    {
        return $this->belongsTo(VendorShop::class, 'vendor_id', 'vendor_id');
    }

    // Define the relationship with product reviews
    public function reviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id', 'product_id');
    }

    // Method to get the average rating of product reviews
    public function getProductReviewsAvg()
    {
        return $this->reviews()->avg('rating');
    }

    // Method to get the count of product reviews
    public function getProductReviewsCount()
    {
        return $this->reviews()->count();
    }

    public function origin()
    {
        return $this->belongsTo(Location::class, 'ships_from', 'id');
    }

    public function offers()
    {
        return $this->hasOne(ProductOffersModel::class, 'offer_product_id', 'product_id');
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class, 'product_id', 'product_id');
    }
}
