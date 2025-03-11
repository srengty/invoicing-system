<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Customer::factory(2)->create();
    }
}
