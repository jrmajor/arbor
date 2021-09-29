<?php

namespace Database\Factories;

use App\Models\Person;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Person>
 */
final class PersonFactory extends Factory
{
    protected $model = Person::class;

    public function definition(): array
    {
        $sex = $this->faker->boolean() ? 'female' : 'male';
        $dead = $this->faker->boolean();

        return [
            'sex' => $sex === 'female' ? 'xx' : 'xy',
            'name' => $this->faker->firstName($sex),
            'middle_name' => $this->faker->boolean() ? $this->faker->firstName($sex) : null,
            'family_name' => $this->faker->lastName($sex),
            'last_name' => $sex === 'female' && $this->faker->boolean() ? $this->faker->lastName($sex) : null,
            'birth_date_from' => $this->faker->dateTimeBetween('-80 years', '-30 years')->format('Y-m-d'),
            'birth_date_to' => fn ($p) => $p['birth_date_from'],
            'birth_place' => $this->faker->city() . ', Polska',
            'dead' => $dead,
            'death_date_from' => $dead ? $this->faker->dateTimeBetween('-29 years', '-5 years')->format('Y-m-d') : null,
            'death_date_to' => fn ($p) => $p['death_date_from'],
            'death_place' => $dead ? $this->faker->city() . ', Polska' : null,
            'funeral_date_from' => fn ($p) => $dead
                ? carbon($p['death_date_from'])
                    ->add(CarbonInterval::days(5))
                    ->format('Y-m-d')
                : null,
            'funeral_date_to' => fn ($p) => $p['funeral_date_from'],
            'funeral_place' => fn ($p) => $p['death_place'],
            'burial_date_from' => fn ($p) => $p['funeral_date_from'],
            'burial_date_to' => fn ($p) => $p['burial_date_from'],
            'burial_place' => fn ($p) => $p['death_place'],
        ];
    }

    public function withParents(): self
    {
        return $this->state([
            'father_id' => Person::factory()->man(),
            'mother_id' => Person::factory()->woman(),
        ]);
    }

    public function withoutParents(Person|int $father = null, Person|int $mother = null): self
    {
        return $this->state([
            'father_id' => $father,
            'mother_id' => $mother,
        ]);
    }

    public function woman(): self
    {
        return $this->state([
            'sex' => 'xx',
            'name' => $this->faker->firstName('female'),
            'middle_name' => $this->faker->boolean() ? $this->faker->firstName('female') : null,
            'family_name' => $this->faker->lastName('female'),
            'last_name' => $this->faker->boolean() ? $this->faker->lastName('female') : null,
        ]);
    }

    public function man(): self
    {
        return $this->state([
            'sex' => 'xy',
            'name' => $this->faker->firstName('male'),
            'middle_name' => $this->faker->boolean() ? $this->faker->firstName('male') : null,
            'family_name' => $this->faker->lastName('male'),
            'last_name' => null,
        ]);
    }

    public function alive(): self
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

    public function dead(): self
    {
        return $this->state([
            'dead' => true,
            'death_date_from' => $this->faker->dateTimeBetween('-29 years', '-5 years')->format('Y-m-d'),
            'death_date_to' => fn ($p) => $p['death_date_from'],
            'death_place' => $this->faker->city() . ', Polska',
            'funeral_date_from' => fn ($p) => carbon($p['death_date_from'])
                ->add(CarbonInterval::days(5))
                ->format('Y-m-d'),
            'funeral_date_to' => fn ($p) => $p['funeral_date_from'],
            'funeral_place' => fn ($p) => $p['death_place'],
            'burial_date_from' => fn ($p) => $p['funeral_date_from'],
            'burial_date_to' => fn ($p) => $p['burial_date_from'],
            'burial_place' => fn ($p) => $p['death_place'],
        ]);
    }
}
