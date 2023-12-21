<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'email_verified_at' => $this->faker->dateTime(),
            'password' => $this->faker->password(),
            'specialty' => $this->faker->word(),
            'bio' => $this->faker->word(),
            'dietary_preferences' => $this->faker->word(),
            'profile_picture' => $this->faker->word(),
            'remember_token' => $this->faker->uuid(),
        ];
    }
}
