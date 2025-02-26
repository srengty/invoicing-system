<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_code')->comment('R1, T1, C1');
            $table->string('category_name_khmer');
            $table->string('category_name_english');
            $table->string('description_khmer')->nullable(); // Make descriptions nullable
            $table->string('description_english')->nullable(); // Make descriptions nullable
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('name_kh')->nullable();
            $table->string('unit');
            $table->decimal('price', 15, 2);
            $table->integer('quantity');
            $table->string('division_id')->nullable()->constrained()->nullOnDelete();
            $table->string('desc')->nullable();
            $table->string('desc_kh')->nullable();
            $table->string('acc_code');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('remark')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
