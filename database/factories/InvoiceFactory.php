<?php

namespace Database\Factories;

use App\Models\Agreement;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Quotation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $count = 1;  
        $year = date('y');
        $subtotal = $this->faker->randomFloat(2, 100, 1000);

        return [
            'invoice_no' => $year . str_pad($count++, 6, '0', STR_PAD_LEFT),
            'agreement_no' => Agreement::inRandomOrder()->value('agreement_no') ?? null, // FIXED 
            'quotation_no' => Quotation::inRandomOrder()->value('quotation_no') ?? null, // FIXED
            'customer_id' => Customer::inRandomOrder()->value('id') ?? null, // FIXED 
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'date' => $this->faker->dateTimeThisYear(),
            'due_date' => $this->faker->dateTimeThisYear(),
            'status' => $this->faker->randomElement(['Pending', 'Paid', 'Cancelled']),
            'sub_total' => $subtotal,
            'grand_total' => $subtotal,
            'products' => json_encode(['product1', 'product2']),
        ];
    }
}
