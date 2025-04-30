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
                'name' => 'Premium Office Chair',
                'name_kh' => 'កៅអីការិយាល័យថ្នាក់ពីរ៉ែម',
                'unit' => 'pcs',
                'price' => 500000,
                'desc' => 'Ergonomic office chair with adjustable height and lumbar support',
                'desc_kh' => 'កៅអីការិយាល័យដែលមានជំនួយខ្នង និងអាចកែកម្ពស់បាន',
                'acc_code' => '73048',
                'remark' => 'Best seller',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'I002',
                'name' => 'Wireless Keyboard',
                'name_kh' => 'ក្តារចុចឥតខ្សែ',
                'unit' => 'pcs',
                'price' => 700000,
                'desc' => 'Bluetooth keyboard with silent keys and 2-year battery life',
                'desc_kh' => 'ក្តារចុចប្លូធូសដែលមានសំលេងស្ងាត់ និងអាចប្រើបាន ២ឆ្នាំ',
                'acc_code' => '73048',
                'remark' => 'New arrival',
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
