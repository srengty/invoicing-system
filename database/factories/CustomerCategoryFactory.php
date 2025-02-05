<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomerCategory>
 */
class CustomerCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_name_khmer' => 'ឯកត្តបុគ្គល',
            'category_name_english' => 'Individual',
            'description_khmer' => 'ឯកត្តបុគ្គល',
            'description_english' => 'Individual',
        ];
    }
}
