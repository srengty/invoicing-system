<?php

namespace Database\Factories;

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
            'code' => $this->faker->randomNumber(5),
            'address' => $this->faker->address(),
            'email' => $this->faker->email(),
            'phone_number' => $this->faker->phoneNumber(),
            'telegram_number' => $this->faker->phoneNumber(),
            'website' => $this->faker->url(),
            'bank_name' => $this->faker->word(),
            'bank_address' => $this->faker->address(),
            'bank_account_name' => $this->faker->name(),
            'bank_account_number' => $this->faker->bankAccountNumber(),
            'bank_swift' => $this->faker->swiftBicNumber(),
        ];
    }
}
