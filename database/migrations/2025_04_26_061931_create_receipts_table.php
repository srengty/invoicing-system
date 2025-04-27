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
            $table->id();
            $table->unsignedBigInteger('invoice_no')->nullable();
            $table->foreign('invoice_no')->references('invoice_no')->on('invoices')->nullOnDelete();
            $table->string('receipt_no')->unique()->comment('Format: R202500001 where 2025 is year and 00001 is sequence');
            $table->date('receipt_date')->useCurrent();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('customer_code', 255)->nullable();
            $table->string('purpose')->nullable();
            $table->decimal('amount_paid', 10, 2);
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
