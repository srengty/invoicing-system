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
            $table->unsignedBigInteger('quotation_no')->unique()->nullable();
            $table->unsignedBigInteger('product_id'); // Assuming products have an ID
            $table->integer('quantity');
            $table->json('product_unit_prices')->nullable();
            $table->decimal('price', 10, 2);
            $table->timestamps();
            // Foreign keys
            $table->foreign('quotation_no')->references('id')->on('quotations')->onDelete('cascade');
            $table->foreign('product_id')->references('id','name','unit')->on('products')->onDelete('cascade');

        });

        Schema::create('price_sales', function (Blueprint $table) {
            $table->id();
            $table->decimal('price');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('product_quotation_id')->constrained('product_quotation')->onDelete('cascade');
            $table->timestamps();
        });



    }

    public function down()
    {
        Schema::dropIfExists('price_sales');
        Schema::dropIfExists('product_quotation');
    }
}
