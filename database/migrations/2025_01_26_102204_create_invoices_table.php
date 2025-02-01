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
            $table->id(); // Primary key
            $table->string('invoice_no')->unique();
            $table->unsignedBigInteger('agreement_no')->nullable();  // Changed to agreement_id for the foreign key
            $table->unsignedBigInteger('quotation_no')->nullable();  // Changed to quotation_id for the foreign key
            $table->unsignedBigInteger('customer_id'); // Foreign key to customer
            $table->string('address');
            $table->string('phone');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('sub_total', 10, 2);
            $table->enum('status', ['Pending', 'Paid', 'Cancelled'])->default('Pending');
            $table->timestamps(); // Created_at and updated_at

            // Foreign key constraints
            $table->foreign('agreement_no')->references('id')->on('agreements')->onDelete('set null');
            $table->foreign('quotation_no')->references('id')->on('quotations')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');  // Assuming cascading delete for customer
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
