<?php

namespace App\Models;

use App\Models\product\ProductModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorShop extends Model
{
    use HasFactory;
    protected $table = 'vendor_shop';
    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'vendor_id';

    protected $fillable = [
        'shop_name',
        'shop_description',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(ProductModel::class, 'vendor_id', 'vendor_id');
    }
}
