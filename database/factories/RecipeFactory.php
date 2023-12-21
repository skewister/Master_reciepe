<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Recipe;

class RecipeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Recipe::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->faker->randomNumber(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->text(),
            'difficulty' => $this->faker->text(),
            'prep_time' => $this->faker->word(),
            'cook_time' => $this->faker->word(),
            'image' => $this->faker->word(),
            'video' => $this->faker->word(),
        ];
    }
}
