<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Event;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Event::class, function (Faker $faker) {
    return [
        'additionel_information' => $faker->text,
        'end_date' => Carbon::parse('+2 weeks'),
        'event_program' => $faker->sentences($nb = 3),
        'is_active' => false,
        'picture' => $faker->imageUrl($width = 640, $height = 480),
        'publish_at' => Carbon::parse('-2 weeks'),
        'start_date' => Carbon::parse('+2 weeks'),
        'start_time' => Carbon::parse('12:00'),
        'subtitle' => $faker->sentence,
        'title' => $faker->word,
    ];
});
