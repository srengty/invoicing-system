<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $this->call([
            ParameterSeeder::class,
            CustomerCategorySeeder::class,
            CustomerSeeder::class,
            QuotationSeeder::class,
            AgreementSeeder::class,
            DivisionSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            InvoiceSeeder::class,
            QuotationProductSeeder::class,
            InvoiceProductSeeder::class,
            PaymentScheduleSeeder::class,
        ]);
    }
}
