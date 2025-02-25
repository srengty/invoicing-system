<?php

namespace Database\Seeders;

use App\Models\Parameter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParameterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Parameter::factory()->create(['parameter_name' => 'quotations-terms-and-conditions', 'parameter_value' => 'Please note that the prices are subject to change without prior notice.']);
        Parameter::factory()->create(['parameter_name' => 'agreements-terms-and-conditions', 'parameter_value' => 'Please note that the prices are subject to change without prior notice.']);
        Parameter::factory()->create(['parameter_name' => 'invoices-terms-and-conditions', 'parameter_value' => 'Please note that the prices are subject to change without prior notice.']);
    }
}
