<?php

namespace Database\Seeders;

use App\Models\CustomerCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CustomerCategory::factory()->createMany([
            [
            'category_name_khmer' => 'ឯកត្តបុគ្គល',
            'category_name_english' => 'Individual',
            'description_khmer' => 'ឯកត្តបុគ្គល',
            'description_english' => 'Individual',
            ],
            [
            'category_name_khmer' => 'អង្គការសាធារណៈ',
            'category_name_english' => 'Public Organization',
            'description_khmer' => 'អង្គការសាធារណៈ',
            'description_english' => 'Public Organization',
            ],
            [
            'category_name_khmer' => 'អង្គការមិនមែនរដ្ឋាភិបាល',
            'category_name_english' => 'NGO',
            'description_khmer' => 'អង្គការមិនមែនរដ្ឋាភិបាល',
            'description_english' => 'NGO',
            ],
            [
            'category_name_khmer' => 'ក្រុមហ៊ុនឯកជន',
            'category_name_english' => 'Private Company',
            'description_khmer' => 'ក្រុមហ៊ុនឯកជន',
            'description_english' => 'Private Company',
            ],

        ]);
    }
}
