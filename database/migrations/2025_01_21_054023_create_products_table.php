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

        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('division_code')->comment('D1, E1, F1');
            $table->string('division_name_khmer');
            $table->string('division_name_english');
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
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->string('desc')->nullable();
            $table->string('desc_kh')->nullable();
            $table->string('acc_code')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('remark')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('divisions');
        Schema::dropIfExists('categories');
    }
};
