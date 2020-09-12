<?php

namespace Database\Factories;

use App\Models\Marriage;
use App\Models\Person;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Factories\Factory;

class MarriageFactory extends Factory
{
    protected $model = Marriage::class;

    public function definition()
    {
        return [
            'woman_id' => Person::factory()->woman()->create()->id,
            'man_id' => Person::factory()->man()->create()->id,
            'rite' => 'roman_catholic',
            'first_event_type' => 'civil_marriage',
            'first_event_date_from' => $this->faker->dateTimeBetween('-40 years', '-20 years')->format('Y-m-d'),
            'first_event_date_to' => fn ($marriage) => $marriage['first_event_date_from'],
            'first_event_place' => $this->faker->city.', Polska',
            'second_event_type' => 'concordat_marriage',
            'second_event_date_from' => function ($marriage) {
                return (new Carbon($marriage['first_event_date_from']))
                    ->add(CarbonInterval::days(3))
                    ->format('Y-m-d');
            },
            'second_event_date_to' => fn ($marriage) => $marriage['second_event_date_from'],
            'second_event_place' => fn ($marriage) => $marriage['first_event_place'],
        ];
    }

    public function divorced()
    {
        return $this->state(function (array $person) {
            return [
                'divorced' => true,
                'divorce_date_from' => $this->faker->dateTimeBetween('-29 years', '-5 years')->format('Y-m-d'),
                'divorce_date_to' => fn ($person) => $person['divorce_date_from'],
                'divorce_place' => $this->faker->city.', Polska',
            ];
        });
    }
}
