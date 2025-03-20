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
        Schema::table('product_quotation', function (Blueprint $table) {
            $table->boolean('include_catalog')->default(false)->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_quotation', function (Blueprint $table) {
            $table->dropColumn('include_catalog');
        });
    }
};
