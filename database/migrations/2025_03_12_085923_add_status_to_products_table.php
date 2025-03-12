<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'status')) { // ✅ Check if column doesn't exist
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('remark');
            }
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'status')) { // ✅ Check if column exists before dropping
                $table->dropColumn('status');
            }
        });
    }
};
