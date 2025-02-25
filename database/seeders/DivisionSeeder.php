<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // TODO: fetch data from Payroll system
        Division::factory()->createMany([
            [
                'division_name_khmer' => 'ទេពកោសល្យព័ត៌មាន',
                'division_name_english' => 'GIC',
                'description_khmer' => 'ទេពកោសល្យព័ត៌មាន',
                'description_english' => 'GIC',
            ],[
                'division_name_khmer' => 'ទេពកោសល្យអគ្គីសនី',
                'division_name_english' => 'GEE',
                'description_khmer' => 'ទេពកោសល្យអគ្គីសនី',
                'description_english' => 'GEE',
            ],
        ]);
    }
}
