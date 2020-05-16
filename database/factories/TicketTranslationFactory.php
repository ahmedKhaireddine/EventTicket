<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Ticket;
use App\TicketTranslation;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(TicketTranslation::class, function (Faker $faker) {
    return [
        'description' => $faker->text,
        'locale' => $faker->randomElement(['en', 'fr']),
        'location' => $faker->sentence,
        'ticket_id' => factory(Ticket::class),
        'type' => $faker->word,
    ];
});
