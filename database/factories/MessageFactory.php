<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Message;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Message::class, function (Faker $faker) {
    return [
        'content' => $faker->sentence,
        'create_at' => Carbon::now(),
        'from_id' => factory(App\User::class),
        'to_id' => factory(App\User::class),
    ];
});
