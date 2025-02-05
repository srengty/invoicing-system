<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentSchedule>
 */
class PaymentScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'agreement_no' => $this->faker->randomElement(\App\Models\Agreement::pluck('agreement_no')->toArray()),
            'due_date' => $this->faker->date(),
            'short_description' => $this->faker->word(),
            'percentage' => $this->faker->randomFloat(2, 0, 100),
            'amount' => $this->faker->randomFloat(2, 0, 100),
            'currency' => $this->faker->word(),
            'status' => $this->faker->randomElement(['Pending', 'Paid', 'Cancelled']),
        ];
    }
}
