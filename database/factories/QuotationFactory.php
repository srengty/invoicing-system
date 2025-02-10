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
<<<<<<< HEAD
        // Get the current count of quotations for the current year dynamically
        $count = Quotation::where('quotation_no', 'like', date('y') . '%')->count() + 1;

        return [
            'quotation_no' => date('y') . str_pad($count, 6, '0', STR_PAD_LEFT),
            'quotation_date' => $this->faker->dateTimeThisYear(),
            'customer_id' => Customer::inRandomOrder()->first()->id ?? null, // Avoids empty pluck issue
=======
        static $counter = 1; // Static counter to keep track of the sequence
        if(Quotation::count() > 0) {
            $counter = Quotation::count() + 1;
        }
        $year = date('y');
        // Ensure the quotation number is unique within the seeding process
        return [
            'quotation_no' => $year . str_pad($counter++, 6, '0', STR_PAD_LEFT),
            'quotation_date' => $this->faker->dateTimeThisYear()->format('Y-m-d'),
            'customer_id' => Customer::inRandomOrder()->value('id') ?? Customer::factory(),
>>>>>>> b97c6ea758ef4faa6e2abf83f2fc039af62147ac
            'address' => $this->faker->address,
            'phone_number' => $this->faker->phoneNumber,
            'terms' => $this->faker->sentence(10),
            'total' => $this->faker->randomFloat(2, 100, 1000),
            'status' => $this->faker->randomElement(['Pending', 'Approved', 'Revise']),
        ];
    }
}
