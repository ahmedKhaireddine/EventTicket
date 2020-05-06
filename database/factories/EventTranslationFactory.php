<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Event;
use App\EventTranslation;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(EventTranslation::class, function (Faker $faker) {
    return [
        'additionel_information' => $faker->text,
        'event_id' => factory(Event::class),
        'event_program' => $faker->sentences($nb = 3),
        'locale' => $faker->randomElement(['en', 'fr']),
        'subtitle' => $faker->sentence,
        'title' => $faker->word,
    ];
});
