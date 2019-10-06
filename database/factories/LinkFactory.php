<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Link::class, function (Faker $faker) {
    return [
        "link" => $faker->url,
        "title" => $faker->name,
    ];
});
