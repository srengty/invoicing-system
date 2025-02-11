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
    }

}
