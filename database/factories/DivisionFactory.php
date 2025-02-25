<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Division>
 */
class DivisionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'division_name_khmer' => $this->faker->word,
            'division_name_english' => $this->faker->word,
            'description_khmer' => $this->faker->sentence,
            'description_english' => $this->faker->sentence,
        ];
    }
}
