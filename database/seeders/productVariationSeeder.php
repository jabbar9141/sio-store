<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\Dimention;
use App\Models\Size;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class productVariationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $colors = [
            [
                'name' => 'Blue',
            ],
            [
                'name' => 'Black',
            ],
            [
                'name' => 'White',
            ],
            [
                'name' => 'Orange',
            ],
            [
                'name' => 'Green',
            ],
            [
                'name' => 'Pink',
            ],
            [
                'name' => 'Red',
            ],
            [
                'name' => 'Yellow',
            ],
        ];

        $sizes = [
            [
                'name' => 'MD',
            ],
            [
                'name' => 'SM',
            ],
            [
                'name' => 'XSM',
            ],
            [
                'name' => 'LG',
            ],
            [
                'name' => 'XLG',
            ],
            [
                'name' => 'XXL',
            ],
          
        ];
        $dimentions = [
            [
                'height' => 12,
                'width' => 12,
                'length' => 12,
                'weight' => 2,
                'product_type' => 'measure',
            ],
            [
                'height' => 13,
                'width' => 13,
                'length' => 13,
                'weight' => 13,
                'product_type' => 'weight',
            ],
            [
                'height' => 15,
                'width' => 15,
                'length' => 15,
                'weight' => 4,
                'product_type' => 'measure',
            ],
            [
                'height' => 16,
                'width' => 16,
                'length' => 16,
                'weight' => 2,
                'product_type' => 'weight',
            ],
            [
                'height' => 17,
                'width' => 17,
                'length' => 17,
                'weight' => 3,
                'product_type' => 'measure',
            ],
            [
                'height' => 12,
                'width' => 12,
                'length' => 12,
                'weight' => 4,
                'product_type' => 'weight',
            ],
            [
                'height' => 18,
                'width' => 18,
                'length' => 18,
                'weight' => 2,
                'product_type' => 'measure',
            ],
            [
                'height' => 20,
                'width' => 20,
                'length' => 20,
                'weight' => 2,
                'product_type' => 'weight',
            ],
            
          
        ];


    foreach($colors as $color){
        Color::create([
            'name' => $color['name'],
        ]);
    };

    foreach($sizes as $size){
        Size::create([
            'name' => $size['name'],
        ]);
    };

    foreach($dimentions as $dimention){
        Dimention::create([
            'height' => $dimention['height'],
            'weight' => $dimention['weight'],
            'length' => $dimention['length'],
            'width' => $dimention['width'],
            'product_type' => $dimention['product_type'],
        ]);
    };

    }


}
