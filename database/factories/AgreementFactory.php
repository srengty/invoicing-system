<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Agreement>
 */
class AgreementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'agreement_no' => $this->faker->randomNumber(5),
            'agreement_ref_no' => $this->faker->randomNumber(5),
            'agreement_date' => $this->faker->dateTimeThisYear(),
            'address' => $this->faker->address(),
            'agreement_doc' => $this->faker->word(),
            'start_date' => $this->faker->dateTimeThisYear(),
            'end_date' => $this->faker->dateTimeThisYear(),
            'amount_no_tax' => $this->faker->randomFloat(2, 0, 1000),
            'tax' => $this->faker->randomFloat(2, 0, 1000),
            'status' => $this->faker->randomElement(['Pending', 'Open', 'Closed']),
            'customer_id' => $this->faker->randomElement(Customer::pluck('id')->toArray()),
        ];
    }
}
