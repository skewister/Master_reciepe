<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Step;

class StepFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Step::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'recipe_id' => $this->faker->randomNumber(),
            'description' => $this->faker->text(),
            'step_number' => $this->faker->numberBetween(-10000, 10000),
            'video' => $this->faker->word(),
        ];
    }
}
