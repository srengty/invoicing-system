<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('invoices', function (Blueprint $table) {
        $table->dropColumn('sub_total'); // Drop the column
    });
}

public function down()
{
    Schema::table('invoices', function (Blueprint $table) {
        $table->decimal('sub_total', 10, 2)->nullable(); // Add the column back if rolling back
    });
}

};
