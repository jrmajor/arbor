<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Person;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Faker\Generator as Faker;

$factory->define(Person::class, function (Faker $faker) {
    $sex = $faker->boolean ? 'female' : 'male';
    $dead = $faker->boolean;

    return [
        'sex' => $sex == 'female' ? 'xx' : 'xy',
        'name' => $faker->firstName($sex),
        'middle_name' => $faker->boolean ? $faker->firstName($sex) : null,
        'family_name' => $faker->lastName($sex),
        'last_name' => $sex == 'female' && $faker->boolean ? $faker->lastName($sex) : null,
        'birth_date' => $faker->dateTimeBetween('-80 years', '-30 years')->format('Y-m-d'),
        'birth_place' => $faker->city . ', Polska',
        'dead' => $dead,
        'death_date' => $dead ? $faker->dateTimeBetween('-29 years', '-5 years')->format('Y-m-d') : null,
        'death_place' => $dead ? $faker->city . ', Polska' : null,
        'funeral_date' => fn(array $person)
            => $dead ? (new Carbon($person['death_date']))->add(CarbonInterval::days(5))->format('Y-m-d') : null,
        'funeral_place' => fn(array $person) => $person['death_place'],
        'burial_date' => fn(array $person) => $person['funeral_date'],
        'burial_place' => fn(array $person) => $person['death_place'],
    ];
});

$factory->state(Person::class, 'woman', function (Faker $faker) {
    return [
        'sex' => 'xx',
        'name' => $faker->firstName('female'),
        'middle_name' => $faker->boolean ? $faker->firstName('female') : null,
        'family_name' => $faker->lastName('female'),
        'last_name' => $faker->boolean ? $faker->lastName('female') : null,
    ];
});

$factory->state(Person::class, 'man', function (Faker $faker) {
    return [
        'sex' => 'xy',
        'name' => $faker->firstName('male'),
        'middle_name' => $faker->boolean ? $faker->firstName('male') : null,
        'family_name' => $faker->lastName('male'),
        'last_name' => null,
    ];
});

$factory->state(Person::class, 'alive', function (Faker $faker) {
    return [
        'dead' => false,
        'death_date' => null,
        'death_place' => null,
        'funeral_date' => null,
        'funeral_place' => null,
        'burial_date' => null,
        'burial_place' => null,
    ];
});

$factory->state(Person::class, 'dead', function (Faker $faker) {
    return [
        'dead' => true,
        'death_date' => $faker->dateTimeBetween('-29 years', '-5 years')->format('Y-m-d'),
        'death_place' => $faker->city . ', Polska',
        'funeral_date' => fn(array $person)
            => (new Carbon($person['death_date']))->add(CarbonInterval::days(5))->format('Y-m-d'),
        'funeral_place' => fn(array $person) => $person['death_place'],
        'burial_date' => fn(array $person) => $person['funeral_date'],
        'burial_place' => fn(array $person) => $person['death_place'],
    ];
});
