<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'iso3',
        'iso2',
        'phonecode',
        'capital',
        'currency',
        'currency_symbol',
        'tld',
        'native',
        'region',
        'subregion',
        'latitude',
        'longitude',
        'emoji',
        'name_in_arabic',
    ];

    public function cities()
    {
        return $this->hasMany(city::class);
    }

    public function shippingCosts()
    {
        return $this->hasMany(CityShippingCost::class);
    }
}
