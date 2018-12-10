<?php

use Faker\Generator as Faker;

$factory->define(App\Ticket::class, function (Faker $faker) {
    return [
        'title' => $faker->text,
        'contact' => $faker->name,
        'status' => $faker->text,
        'issue' => $faker->text
    ];
});
