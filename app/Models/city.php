<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class city extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_in_arabic',
        'state_id',
        'country_id',
        'state_code',
        'country_code',
        'latitude',
        'longitude',
        'flag',
        'wikiDataId',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
