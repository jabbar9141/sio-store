<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityShippingCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'city_id',
        'weight',
        'cost',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
