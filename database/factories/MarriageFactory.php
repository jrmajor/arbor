<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Marriage;
use App\Person;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Faker\Generator as Faker;

$factory->define(Marriage::class, function (Faker $faker) {
    return [
        'woman_id' => factory(Person::class)->state('woman')->create()->id,
        'man_id' => factory(Person::class)->state('woman')->create()->id,
        'rite' => 'roman_catholic',
        'first_event_type' => 'civil_marriage',
        'first_event_date_from' => $faker->dateTimeBetween('-40 years', '-20 years')->format('Y-m-d'),
        'first_event_date_to' => fn (array $marriage) => $marriage['first_event_date_from'],
        'first_event_place' => $faker->city . ', Polska',
        'second_event_type' => 'concordat_marriage',
        'second_event_date_from' => fn (array $marriage)
            => (new Carbon($marriage['first_event_date_from']))->add(CarbonInterval::days(3))->format('Y-m-d'),
        'second_event_date_to' => fn (array $marriage) => $marriage['second_event_date_from'],
        'second_event_place' => fn (array $marriage) => $marriage['first_event_place'],
    ];
});

$factory->state(Marriage::class, 'ended', function (Faker $faker) {
    return [
        'ended' => true,
        'end_date_from' => $faker->dateTimeBetween('-29 years', '-5 years')->format('Y-m-d'),
        'end_date_to' => fn (array $person) => $person['end_date_from'],
    ];
});
