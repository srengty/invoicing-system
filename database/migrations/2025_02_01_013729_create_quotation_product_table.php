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
            $table->unsignedBigInteger('quotation_no');
            $table->unsignedBigInteger('product_id'); // Assuming products have an ID
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamps();     
            // Foreign keys
            $table->foreign('quotation_no')->references('quotation_no')->on('quotations')->onDelete('cascade');
            $table->foreign('product_id')->references('id','name','unit')->on('products')->onDelete('cascade');

        });
        
    }

    public function down()
    {
        Schema::dropIfExists('product_quotation');
    }
}
