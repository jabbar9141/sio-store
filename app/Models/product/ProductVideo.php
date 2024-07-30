<?php

namespace App\Models\product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVideo extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'product_video';
    protected $primaryKey = 'video_id';
    public $timestamps = false;
}
