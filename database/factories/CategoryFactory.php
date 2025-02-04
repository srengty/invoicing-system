<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_code' => $this->faker->word(),
            'category_name_khmer' => $this->faker->word(),
            'category_name_english' => $this->faker->word(),
            'description_khmer' => $this->faker->word(),
            'description_english' => $this->faker->word(),
        ];
    }
}
