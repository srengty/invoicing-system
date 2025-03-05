<?php

namespace Database\Factories;

use App\Models\CustomerCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'customer_category_id' => $this->faker->randomElement(CustomerCategory::pluck('id')->toArray()),
            'code' => $this->faker->unique()->word(),
            'credit_period' => 15,
            'address' => $this->faker->address(),
            'website' => $this->faker->url(),
            // ------------------------------------------
            'contact_person' => $this->faker->name(),
            'email' => $this->faker->email(),
            'phone_number' => $this->faker->phoneNumber(),
            'telegram_number' => $this->faker->phoneNumber(),
            // ------------------------------------------
            'bank_name' => $this->faker->word(),
            'bank_address' => $this->faker->address(),
            'bank_account_name' => $this->faker->name(),
            'bank_account_number' => $this->faker->bankAccountNumber(),
            'bank_swift' => $this->faker->swiftBicNumber(),
        ];
    }
}
