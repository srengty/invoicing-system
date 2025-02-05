<?php

namespace Database\Seeders;

use App\Models\Agreement;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Quotation;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        Invoice::factory(10)->create();
        /* Invoice::create([
            'invoice_no' => '25000001',
            'customer_id' => 1, // Reference a customer
            'agreement_no' => Quotation::all(['id'])->first()->id, // Reference an agreement
            'quotation_no' => '160731', // Reference a quotation
            'address' => '123 Customer St.',
            'phone' => '1234567890',
            'date' => now(),
            'due_date' => now()->addMonth(),
            'sub_total' => 100.0,
        ]); */
        // Add more invoices as needed
    }

}
