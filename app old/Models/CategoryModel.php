<?php

namespace App\Models;

use App\Models\product\ProductModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryModel extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $table = 'category';
    public $timestamps = false;
    protected $primaryKey = 'category_id';

    protected $fillable = [
        'category_name',
        'category_image'
    ];

    public function products()
    {
        return $this->hasMany(ProductModel::class, 'category_id', 'category_id');
    }
}
