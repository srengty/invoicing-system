<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationProductTable extends Migration
{
    public function up()
    {
        Schema::create('quotation_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity')->default(1);
            // Optional: Add price or subtotal columns if needed
            // $table->decimal('price', 10, 2);
            // $table->decimal('subtotal', 10, 2);
            $table->timestamps();

            $table->foreign('quotation_id')
                  ->references('id')->on('quotations')
                  ->onDelete('cascade');

            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->onDelete('cascade');

            // Ensure that each product is only added once per quotation (if desired)
            $table->unique(['quotation_id', 'product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('quotation_product');
    }
}
