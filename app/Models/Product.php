<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Sushi\Sushi;

class Product extends Model
{
    use HasFactory, Sushi;

    /**
     * Model Rows
     *
     * @return void
     */
    public function getRows()
    {
        //API
        $products = Http::get('https://dummyjson.com/products')->json();

        //filtering some attributes
        $products = Arr::map($products['products'], function ($item) {
            return Arr::only(
                $item,
                [
                    'id',
                    'title',
                    'description',
                    'price',
                    'rating',
                    // 'warrantyInformation',
                    'category',
                    'thumbnail',
                ]
            );
        });

        return $products;
    }

    protected $table = 'product';
    protected $fillable = [
        'title',
        'description',
        'price',
        'rating',
        'category',
    ];
}
