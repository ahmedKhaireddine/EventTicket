<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Event;
use App\Ticket;
use Faker\Generator as Faker;

$factory->define(Ticket::class, function (Faker $faker) {
    return [
        'event_id' => factory(Event::class),
        'price' => $faker->numberBetween($min = 1000, $max = 4000),
        'tickets_number' => 50000,
        'tickets_remain' => 50000,
    ];
});

