<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Address;
use Faker\Generator as Faker;

$factory->define(Address::class, function (Faker $faker) {
    return [
        'additionel_information' => $faker->secondaryAddress,
        'city' => $faker->city,
        'country' => $faker->country ,
        'postal_code' => $faker->postcode,
        'street_address' => $faker->streetAddress,
        'venue' => $faker->company
    ];
});
