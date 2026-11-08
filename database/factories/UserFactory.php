<?php

namespace Database\Factories;

use App\Models\User;
use Faker\Generator as Faker;
use Faker\Provider\Internet as InternetFaker;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 *
 * @property Faker&InternetFaker $faker
 *
 * @phpstan-ignore propertyTag.unresolvableType
 */
#[UseModel(User::class)]
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'username' => $this->faker->unique()->username(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password',
            'permissions' => 0,
            'remember_token' => Str::random(10),
        ];
    }
}
