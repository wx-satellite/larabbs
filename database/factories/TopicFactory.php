<?php
/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Faker\Generator as Faker;

$factory->define(App\Models\Topic::class, function (Faker $faker) {

    // sentence生成是段文本，text生成是长文本
    $short_content = $faker->sentence();

    $created_time = $faker->dateTimeThisMonth();

    $updated_time = $faker->dateTimeThisMonth($created_time);


    return [
        "title" => $short_content,
        "body" => $faker->text(),
        "excerpt" => $short_content,
        "created_at" => $created_time,
        "updated_at" => $updated_time
    ];
});
