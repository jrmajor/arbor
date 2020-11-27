<?php

namespace Database\Factories;

use App\Models\Person;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonFactory extends Factory
{
    protected $model = Person::class;

    public function definition()
    {
        $sex = $this->faker->boolean ? 'female' : 'male';
        $dead = $this->faker->boolean;

        return [
            'sex' => $sex === 'female' ? 'xx' : 'xy',
            'name' => $this->faker->firstName($sex),
            'middle_name' => $this->faker->boolean ? $this->faker->firstName($sex) : null,
            'family_name' => $this->faker->lastName($sex),
            'last_name' => $sex === 'female' && $this->faker->boolean ? $this->faker->lastName($sex) : null,
            'birth_date_from' => $this->faker->dateTimeBetween('-80 years', '-30 years')->format('Y-m-d'),
            'birth_date_to' => fn ($person) => $person['birth_date_from'],
            'birth_place' => $this->faker->city.', Polska',
            'dead' => $dead,
            'death_date_from' => $dead ? $this->faker->dateTimeBetween('-29 years', '-5 years')->format('Y-m-d') : null,
            'death_date_to' => fn ($person) => $person['death_date_from'],
            'death_place' => $dead ? $this->faker->city.', Polska' : null,
            'funeral_date_from' => function ($person) use ($dead) {
                return $dead
                    ? (new Carbon($person['death_date_from']))
                        ->add(CarbonInterval::days(5))
                        ->format('Y-m-d')
                    : null;
            },
            'funeral_date_to' => fn ($person) => $person['funeral_date_from'],
            'funeral_place' => fn ($person) => $person['death_place'],
            'burial_date_from' => fn ($person) => $person['funeral_date_from'],
            'burial_date_to' => fn ($person) => $person['burial_date_from'],
            'burial_place' => fn ($person) => $person['death_place'],
        ];
    }

    public function woman()
    {
        return $this->state(function () {
            return [
                'sex' => 'xx',
                'name' => $this->faker->firstName('female'),
                'middle_name' => $this->faker->boolean ? $this->faker->firstName('female') : null,
                'family_name' => $this->faker->lastName('female'),
                'last_name' => $this->faker->boolean ? $this->faker->lastName('female') : null,
            ];
        });
    }

    public function man()
    {
        return $this->state(function () {
            return [
                'sex' => 'xy',
                'name' => $this->faker->firstName('male'),
                'middle_name' => $this->faker->boolean ? $this->faker->firstName('male') : null,
                'family_name' => $this->faker->lastName('male'),
                'last_name' => null,
            ];
        });
    }

    public function alive()
    {
        return $this->state([
            'dead' => false,
            'death_date_from' => null,
            'death_date_to' => null,
            'death_place' => null,
            'funeral_date_from' => null,
            'funeral_date_to' => null,
            'funeral_place' => null,
            'burial_date_from' => null,
            'burial_date_to' => null,
            'burial_place' => null,
        ]);
    }

    public function dead()
    {
        return $this->state(function ($person) {
            return [
                'dead' => true,
                'death_date_from' => $this->faker->dateTimeBetween('-29 years', '-5 years')->format('Y-m-d'),
                'death_date_to' => fn ($person) => $person['death_date_from'],
                'death_place' => $this->faker->city.', Polska',
                'funeral_date_from' => function ($person) {
                    return (new Carbon($person['death_date_from']))
                        ->add(CarbonInterval::days(5))
                        ->format('Y-m-d');
                },
                'funeral_date_to' => fn ($person) => $person['funeral_date_from'],
                'funeral_place' => fn ($person) => $person['death_place'],
                'burial_date_from' => fn ($person) => $person['funeral_date_from'],
                'burial_date_to' => fn ($person) => $person['burial_date_from'],
                'burial_place' => fn ($person) => $person['death_place'],
            ];
        });
    }
}
