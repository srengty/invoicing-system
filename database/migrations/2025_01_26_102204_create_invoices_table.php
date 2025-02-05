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
            $table->foreignId('agreement_no')->nullable()->constrained('agreements','agreement_no')->nullOnDelete();  // Changed to agreement_id for the foreign key
            $table->foreignId('quotation_no')->nullable()->constrained('quotations','quotation_no')->nullOnDelete();  // Changed to quotation_id for the foreign key
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete(); // Foreign key to customer
            $table->string('address');
            $table->string('phone');
            $table->date('date');
            $table->date('due_date');
            $table->enum('status', ['Pending', 'Paid', 'Cancelled'])->default('Pending');
            $table->decimal('sub_total', 10, 2);
            $table->decimal('grand_total', 10, 2)->nullable();
            $table->json('products')->nullable(); // Add the products column
            $table->timestamps(); // Created_at and updated_at
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
