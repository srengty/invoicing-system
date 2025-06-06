<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHdStatusAndRmStatusToInvoicesTable extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('hdStatus')->nullable(); // Add hdStatus column
            $table->string('rmStatus')->nullable(); // Add rmStatus column
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('hdStatus'); // Drop hdStatus column if the migration is rolled back
            $table->dropColumn('rmStatus'); // Drop rmStatus column if the migration is rolled back
        });
    }
}
