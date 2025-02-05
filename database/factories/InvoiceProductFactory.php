<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceProduct>
 */
class InvoiceProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_no' => $this->faker->randomElement(Invoice::pluck('invoice_no')->toArray()),
            'product_id' => $this->faker->randomElement(Product::pluck('id')->toArray()),
            'quantity' => $this->faker->randomNumber(2),
        ];
    }
}
