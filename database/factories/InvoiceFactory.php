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
        static $count = Invoice::where('invoice_no','>',date('y'))->count() + 1;
        $subtotal = $this->faker->randomFloat(2, 100, 1000);
        return [
            'invoice_no' => date('y').str_pad($count++, 6, '0', STR_PAD_LEFT),
            'agreement_no' => $this->faker->randomElement(Agreement::pluck('agreement_no')->toArray()),
            'quotation_no' => $this->faker->randomElement(Quotation::pluck('quotation_no')->toArray()),
            'customer_id' => $this->faker->randomElement(Customer::pluck('id')->toArray()),
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
