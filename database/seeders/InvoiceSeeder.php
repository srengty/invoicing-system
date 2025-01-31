<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Customer;
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
        Invoice::create([
            'invoice_no' => '1',
            'customer_id' => 1, // Reference a customer
            'agreement_no' => '87783', // Reference an agreement
            'quotation_no' => '160731', // Reference a quotation
            'address' => '123 Customer St.',
            'phone' => '1234567890',
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'sub_total' => 100.0,
        ]);
        // Add more invoices as needed
    }

}
