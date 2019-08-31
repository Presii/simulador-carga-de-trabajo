<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Personal;
use Faker\Generator as Faker;

$factory->define(Personal::class, function (Faker $faker) {
    return [
        'Salario_tarea' => $faker->randomDigitNotNull,
        'idMes' => $faker->randomDigitNotNull,
        'Capacidad' => 4,
        'name'=> $faker->firstName,
        'last_name' => $faker->lastName
    ];
});
