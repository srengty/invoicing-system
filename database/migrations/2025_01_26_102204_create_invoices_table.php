<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_no')->primary();
            $table->foreignId('agreement_no')->nullable()->constrained('agreements','agreement_no')->nullOnDelete();
            $table->foreignId('quotation_no')->nullable()->constrained('quotations','quotation_no')->nullOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('address');
            $table->string('phone');
            $table->date('date');
            $table->date('due_date');
            $table->enum('status', ['Pending', 'Paid', 'Cancelled'])->default('Pending');
            $table->decimal('sub_total', 10, 2);
            $table->decimal('grand_total', 10, 2)->nullable();
            $table->json('products')->nullable();
            $table->enum('currency', ['USD', 'KHR'])->default('USD'); // Add currency column with default USD
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
