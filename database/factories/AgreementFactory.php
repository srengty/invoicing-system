<?php

namespace Database\Factories;

use App\Models\Agreement;
use App\Models\Customer;
use App\Models\Quotation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Agreement>
 */
class AgreementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $count = 1; // Fixed: No more invalid database query in static
        if(Agreement::count() > 0) {
            $count = Agreement::count() + 1;
        }
        $year = date('y');

        $status = $this->faker->randomElement(['Open', 'Closed', 'Abnormal Closed']);

        return [
            'quotation_no' => Quotation::inRandomOrder()->value('quotation_no') ?? null, // Fixed: More efficient
            'agreement_no' => date('y').str_pad($count++, 6, '0', STR_PAD_LEFT),
            'agreement_ref_no' => $this->faker->randomNumber(5),
            'agreement_date' => $this->faker->dateTimeThisYear()->format('d/m/Y'),
            'address' => $this->faker->address(),
            'agreement_doc' => $this->faker->word(),
            'attachments' => json_encode(['file1', 'file2']),
            'start_date' => $this->faker->dateTimeThisYear()->format('d/m/Y'),
            'end_date' => $this->faker->dateTimeThisYear()->format('d/m/Y'),
            'amount' => $this->faker->randomFloat(2, 0, 1000),
            'status' => $status,
            'short_description' => $this->faker->sentence(),
            'close_reason' => $status=='Abnormal Closed'?$this->faker->sentence():null,
            'customer_id' => $this->faker->randomElement(Customer::pluck('id')->toArray()),
            'due_payment' => $this->faker->randomFloat(2, 0, 1000),
            'total_progress_payment' => $this->faker->randomFloat(2, 0, 1000),
            'total_progress_payment_percentage' => $this->faker->randomFloat(2, 0, 100),
        ];
    }
}
