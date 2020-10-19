<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(App\Todo::class, function (Faker $faker) {
    return [
        //
        'user_id' => 1,
        'title' => $faker->text(50),
        'description' => $faker->text(400),
        'completed' => rand(0,1),
    ];
});
