<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Added this import
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get existing division and category IDs

        $products = [
            [
                'code' => 'I001',
                'name' => 'Lab 01',
                'name_kh' => 'មន្ទីរពិសោធន៍០១',
                'unit' => 'pcs',
                'price' => 500000,
                'desc' => 'Building F',
                'desc_kh' => 'អគារ F',
                'acc_code' => '73048 ផលពីសេវាផ្សេងៗ',
                'remark' => 'Lab',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'I002',
                'name' => 'Lab 02',
                'name_kh' => 'មន្ទីរពិសោធន៍០២',
                'unit' => 'pcs',
                'price' => 700000,
                'desc' => 'Building F',
                'desc_kh' => 'អគារ F',
                'acc_code' => '73048 ផលពីសេវាផ្សេងៗ' ,
                'remark' => 'New Lab',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
