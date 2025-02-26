<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // ✅ Add `pdf_url` if it doesn't already exist
            if (!Schema::hasColumn('products', 'pdf_url')) {
                $table->string('pdf_url')->nullable()->after('desc_kh'); // Adjust position if needed
            }
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // ✅ Rollback by dropping `pdf_url` only if it exists
            if (Schema::hasColumn('products', 'pdf_url')) {
                $table->dropColumn('pdf_url');
            }
        });
    }
};

