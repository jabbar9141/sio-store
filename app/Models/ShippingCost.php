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

    public function country()
    {
        return Country::where('iso2', $this->country_iso_2)->first();
    }
}
