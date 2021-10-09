<?php

namespace Database\Factories;

use App\Models\User;
use Faker\Generator as Faker;
use Faker\Provider\Internet as InternetFaker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /** @var Faker|InternetFaker */
    protected $faker;

    public function definition(): array
    {
        return [
            'username' => $this->faker->username(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'permissions' => 0,
            'remember_token' => Str::random(10),
        ];
    }
}
