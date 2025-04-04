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
            $table->foreignId('agreement_no')->nullable()->constrained('agreements','agreement_no')->nullOnDelete();
            $table->foreignId('quotation_no')->nullable()->constrained('quotations','quotation_no')->nullOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('address');
            $table->string('phone_number');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['Pending', 'Paid', 'Cancelled'])->default('Pending');
            $table->double('total')->default(0)->comment('no tax');
            $table->double('total_usd', 15, 2)->nullable();
            $table->decimal('exchange_rate', 10, 4)->nullable();
            $table->json('products')->nullable();
            $table->enum('currency', ['USD', 'KHR'])->default('USD'); // Add currency column with default USD
            $table->text('terms')->nullable();
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
