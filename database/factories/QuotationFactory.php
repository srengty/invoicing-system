<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Quotation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quotation>
 */
class QuotationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Quotation::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quotation_no' => $this->faker->unique()->randomNumber(6),
            'quotation_date' => $this->faker->dateTimeThisYear(),
            // 'customer_id' => Customer::factory(), // Assumes a Customer factory exists
            'customer_id' => $this->faker->randomElement(Customer::pluck('id')->toArray()),
            'address' => $this->faker->address,
            'phone_number' => $this->faker->phoneNumber,
            'terms' => $this->faker->sentence(10),
            'total' => $this->faker->randomFloat(2, 100, 1000),
            'tax' => $this->faker->randomFloat(2, 5, 50),
            'grand_total' => $this->faker->randomFloat(2, 150, 1100),
            'status' => $this->faker->randomElement(['Pending', 'Approved', 'Revise']),
            'customer_status' => $this->faker->randomElement(['Pending', 'Sent', 'Rejected']),
        ];
    }
}
