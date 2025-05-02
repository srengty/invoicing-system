<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            // $table->id(); // Remove this line
            $table->unsignedBigInteger('receipt_no')->primary()->comment('25000001:year 2025, agreement no 1');
            $table->foreignId('invoice_no')->nullable()->constrained('invoices', 'invoice_no')->nullOnDelete();
            $table->date('receipt_date')->useCurrent();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('customer_code', 255)->nullable();
            $table->string('purpose')->nullable();
            $table->decimal('paid_amount', 10, 2);
            $table->string('amount_in_words')->nullable();
            $table->string('payment_method');
            $table->string('payment_reference_no')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
