<?php

namespace Database\Factories;

use App\Enums\Sex;
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
        return [
            'sex' => $this->faker->randomElement([Sex::Male, Sex::Female, null]),
            'name' => fn (array $p) => $this->faker->firstName($p['sex']?->valueForFaker()),
            'middle_name' => fn (array $p) => $this->faker->boolean()
                ? $this->faker->firstName($p['sex']?->valueForFaker()) : null,
            'family_name' => fn (array $p) => $this->faker->lastName($p['sex']?->valueForFaker()),
            'last_name' => fn (array $p) => $p['sex'] === Sex::Female && $this->faker->boolean()
                ? $this->faker->lastName($p['sex']?->valueForFaker()) : null,
            'birth_date_from' => $this->faker->dateTimeBetween('-80 years', '-30 years')->format('Y-m-d'),
            'birth_date_to' => fn (array $p) => $p['birth_date_from'],
            'birth_place' => $this->faker->city() . ', Polska',
            'dead' => $this->faker->boolean(),
            'death_date_from' => fn (array $p) => $p['dead']
                ? $this->faker->dateTimeBetween('-29 years', '-5 years')->format('Y-m-d') : null,
            'death_date_to' => fn (array $p) => $p['death_date_from'],
            'death_place' => fn (array $p) => $p['dead'] ? $this->faker->city() . ', Polska' : null,
            'funeral_date_from' => fn (array $p) => $p['dead']
                ? carbon($p['death_date_from'])
                    ->add(CarbonInterval::days(5))
                    ->format('Y-m-d')
                : null,
            'funeral_date_to' => fn (array $p) => $p['funeral_date_from'],
            'funeral_place' => fn (array $p) => $p['death_place'],
            'burial_date_from' => fn (array $p) => $p['funeral_date_from'],
            'burial_date_to' => fn (array $p) => $p['burial_date_from'],
            'burial_place' => fn (array $p) => $p['death_place'],
        ];
    }

    public function withParents(): self
    {
        return $this->state([
            'father_id' => Person::factory()->male(),
            'mother_id' => Person::factory()->female(),
        ]);
    }

    public function withoutParents(Person|int $father = null, Person|int $mother = null): self
    {
        return $this->state([
            'father_id' => $father,
            'mother_id' => $mother,
        ]);
    }

    public function male(): self
    {
        return $this->state(['sex' => Sex::Male]);
    }

    public function female(): self
    {
        return $this->state(['sex' => Sex::Female]);
    }

    public function alive(): self
    {
        return $this->state(['dead' => false]);
    }

    public function dead(): self
    {
        return $this->state(['dead' => true]);
    }
}
