<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(App\ArticleModel::class, function (Faker $faker) {
    return [
        //
        'title' => $faker->text(30),
        'body' => $faker->text(200)
    ];
});
