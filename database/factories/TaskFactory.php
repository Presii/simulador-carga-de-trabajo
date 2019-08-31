<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Task;
use Faker\Generator as Faker;

$factory->define(Task::class, function (Faker $faker) {
    return [
        'Descripcion_tarea' => $faker->decimal,
        'Clasificacion' => $faker->randomDigitNotNull
    ];
});
