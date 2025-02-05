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
        static $number = Quotation::where('quotation_no','>',date('y'))->count() + 1;
        return [
            'quotation_no' => date('y').str_pad($number++, 6, '0', STR_PAD_LEFT),
            'quotation_date' => $this->faker->dateTimeThisYear(),
            'customer_id' => $this->faker->randomElement(Customer::pluck('id')->toArray()),
            'address' => $this->faker->address,
            'phone_number' => $this->faker->phoneNumber,
            'terms' => $this->faker->sentence(10),
            'total' => $this->faker->randomFloat(2, 100, 1000),
            'status' => $this->faker->randomElement(['Pending', 'Approved', 'Revise']),
        ];
    }
}
