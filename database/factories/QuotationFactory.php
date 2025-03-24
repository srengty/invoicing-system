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
        static $counter = 1; // ✅ Correct placement outside return array
        $year = date('y');

        return [
            'quotation_no' => $year . str_pad($counter++, 6, '0', STR_PAD_LEFT),
            'quotation_date' => $this->faker->dateTimeThisYear()->format('Y-m-d'),
            'customer_id' => Customer::inRandomOrder()->value('id') ?? Customer::factory(),
            'address' => $this->faker->address,
            'phone_number' => $this->faker->phoneNumber,
            'terms' => $this->faker->sentence(2),
            'total' => $this->faker->randomFloat(2, 100, 1000),
            'status' => $this->faker->randomElement(['Pending', 'Approved', 'Revise']),
        ];
    }
}
