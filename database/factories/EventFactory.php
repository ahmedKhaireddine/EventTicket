<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Event;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Event::class, function (Faker $faker) {
    return [
        'additionel_information' => $faker->text,
        'date' => Carbon::parse('+2 weeks'),
        'is_active' => $faker->boolean,
        'picture' => $faker->imageUrl($width = 640, $height = 480),
        'publish_at' => Carbon::parse('-2 weeks'),
        'subtitle' => $faker->sentence,
        'title' => $faker->word,
    ];
});
