<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Ecommerce\Products;
use Faker\Generator as Faker;

$factory->define(Products::class, function (Faker $faker) {
    $productNames = [
        'T-shart', 'Jeans', 'Polo T-Shart', 'Kamich', '3 pis', 'Urna', 'Shoe', 'Keds',
        'Mango', 'Bananna', 'Jack Fruits',
        'Fulkopi', 'Gajor', 'Qucumber'
    ];


    return [
        'title' => $faker->randomElements( $productNames ),
        'created_at' => [$faker->date()],
        'updated_at' => [$faker->date()],
        'deleted_at' => null,
        'unit_price' => rand(100, 500)

    ];
});
