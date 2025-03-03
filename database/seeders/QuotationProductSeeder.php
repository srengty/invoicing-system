<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Quotation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuotationProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample data: Add products to quotations
        DB::table('product_quotation')->insert([
            [
                'quotation_no' => fake()->randomElement(Quotation::pluck('id')),
                'product_id' => fake()->randomElement(Product::pluck('id')),
                'quantity' => 2,
                'price' => 100.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'quotation_no' => fake()->randomElement(Quotation::pluck('id')),
                'product_id' => fake()->randomElement(Product::pluck('id')),
                'quantity' => 1,
                'price' => 50.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'quotation_no' => fake()->randomElement(Quotation::pluck('id')),
                'product_id' => fake()->randomElement(Product::pluck('id')),
                'quantity' => 5,
                'price' => 200.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
