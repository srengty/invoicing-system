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
            $table->id();
            $table->unsignedBigInteger('invoice_no')->unique()->nullable();
            $table->timestamp('invoice_date')->nullable();
            $table->foreignId('agreement_no')->nullable()->constrained('agreements','agreement_no')->nullOnDelete();
            $table->foreignId('quotation_no')->nullable()->constrained('quotations','quotation_no')->nullOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('address');
            $table->string('phone');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status', 20)->default('Pending');
            $table->decimal('grand_total', 10, 2)->nullable();
            $table->json('products')->nullable();
            $table->enum('currency', ['USD', 'KHR'])->default('USD');
            $table->decimal('exchange_rate', 15, 2)->nullable();
            $table->decimal('total_usd', 15, 2)->nullable();
            $table->string('terms')->nullable();
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
