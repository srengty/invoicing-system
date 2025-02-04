<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationProductTable extends Migration
{
    public function up()
    {
        Schema::create('product_quotation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            // Optional: Add price or subtotal columns if needed
            // $table->decimal('price', 10, 2);
            // $table->decimal('subtotal', 10, 2);
            $table->timestamps();

            // Ensure that each product is only added once per quotation (if desired)
            $table->unique(['quotation_id', 'product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_quotation');
    }
}
