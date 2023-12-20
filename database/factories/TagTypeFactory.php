<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\TagType;

class TagTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TagType::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->word(),
        ];
    }
}
