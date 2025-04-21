<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Migration for pivot table
    public function up()
    {
        Schema::create('invoice_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_no')->unique()->nullable();
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->json('product_unit_prices')->nullable();
            $table->decimal('price', 10, 2);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('invoice_no')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_product');
        Schema::dropIfExists('invoice_product');
    }
};
