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
            'first_event_date_to' => fn ($m) => $m['first_event_date_from'],
            'first_event_place' => $this->faker->city.', Polska',
            'second_event_type' => 'concordat_marriage',
            'second_event_date_from' => fn ($m) => carbon($m['first_event_date_from'])
                ->add(CarbonInterval::days(3))
                ->format('Y-m-d'),
            'second_event_date_to' => fn ($m) => $m['second_event_date_from'],
            'second_event_place' => fn ($m) => $m['first_event_place'],
        ];
    }

    public function divorced(): self
    {
        return $this->state([
            'divorced' => true,
            'divorce_date_from' => $this->faker->dateTimeBetween('-29 years', '-5 years')->format('Y-m-d'),
            'divorce_date_to' => fn ($m) => $m['divorce_date_from'],
            'divorce_place' => $this->faker->city.', Polska',
        ]);
    }
}
