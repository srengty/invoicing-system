<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Product Category Seeder
 * @package Database\Seeders
 */
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory()->createMany([
            [
                'category_name_khmer' => 'សេវាកម្មជួល',
                'category_name_english' => 'Rental Service',
                'description_khmer' => 'សេវាកម្មជួល',
                'description_english' => 'Rental Service',
            ],
            [
                'category_name_khmer' => 'បង្រៀន',
                'category_name_english' => 'Training Service',
                'description_khmer' => 'បង្រៀន',
                'description_english' => 'Training Service',
            ],
            [
                'category_name_khmer' => 'សេវាកម្មប្រឹក្សា',
                'category_name_english' => 'Consultation Service',
                'description_khmer' => 'សេវាកម្មប្រឹក្សា',
                'description_english' => 'Consultation Service',
            ],
        ]);
    }
}
