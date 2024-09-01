<?php

namespace Database\Factories;

use App\Models\User;
use Faker\Generator as Faker;
use Faker\Provider\Internet as InternetFaker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 *
 * @property Faker&InternetFaker $faker
 *
 * @phpstan-ignore propertyTag.unresolvableType
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'username' => $this->faker->unique()->username(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'permissions' => 0,
            'remember_token' => Str::random(10),
        ];
    }
}
