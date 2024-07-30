<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'weight',
        'country_name',
        'country_iso_2',
        'cost',
    ];
}
