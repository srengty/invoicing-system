<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Add this import

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $customers = [
            [
                'name' => 'POM MOUYLANG',
                'code' => 'C001',
                'credit_period' => 15,
                'address' => '123 Main Street, New York, NY 10001',
                'website' => 'https://moodle.ccun.edu.kh/',
                'contact_person' => 'POM MOUYLANG',
                'email' => 'mouylangpom@gmail.com',
                'phone_number' => '012662033',
                'username' => 'POM_MOUYLANG',
                'telegram_number' => '012662033',
                'bank_name' => 'Chase Bank',
                'bank_address' => '456 Park Ave, New York, NY 10022',
                'bank_account_name' => 'POM MOUYLANG',
                'bank_account_number' => '012662033',
                'bank_swift' => 'CHASUS33',
                'active' => true,
            ],
            [
                'name' => 'DIN PICH',
                'code' => 'C002',
                'credit_period' => 20,
                'address' => '789 Industrial Park, Chicago, IL 60601',
                'website' => 'https://moodle.ccun.edu.kh/',
                'contact_person' => 'DIN PICH',
                'email' => 'monopich1823@gmail.com',
                'phone_number' => '086318261',
                'username' => 'monopich',
                'telegram_number' => '086318261',
                'bank_name' => 'Bank of America',
                'bank_address' => '500 W Madison St, Chicago, IL 60661',
                'bank_account_name' => 'DIN PICH',
                'bank_account_number' => '012662033',
                'bank_swift' => 'BOFAUS3N',
                'active' => true,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
