<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InvoiceProduct;
use App\Models\Invoice;
use App\Models\Product;

class InvoiceProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        InvoiceProduct::factory(10)->create();
        // Ensure the invoice and product exist, or create them dynamically.
        // Here, we are assuming invoices and products with IDs 1 and 2 already exist.

        // You can create invoices and products dynamically like this if they don't exist yet:
        // $invoice = Invoice::create([
        //     'customer_id' => 1, // Replace with actual customer ID
        //     'invoice_no' => 'INV-001', // Example invoice number
        //     'amount' => 100, // Example amount
        //     'due_date' => now()->addDays(30), // Example due date
        // ]);

        // $product1 = Product::create([
        //     'name' => 'Product 1',
        //     'unit' => 'pcs',
        //     'price' => 20.00,
        // ]);

        // $product2 = Product::create([
        //     'name' => 'Product 2',
        //     'unit' => 'pcs',
        //     'price' => 10.00,
        // ]);

        // Add invoice-product associations.
        /* InvoiceProduct::create([
            'invoice_id' => 1, // Reference an existing invoice
            'product_id' => 1, // Reference an existing product
            'quantity' => 2,
        ]);

        InvoiceProduct::create([
            'invoice_id' => 1, // Reference another invoice
            'product_id' => 2, // Reference another product
            'quantity' => 1,
        ]); */

        // Add more invoice-product associations as needed
    }
}
