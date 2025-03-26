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
    Schema::table('quotations', function (Blueprint $table) {
        $table->string('customer_status_comment')->nullable();
    });
}

public function down()
{
    Schema::table('quotations', function (Blueprint $table) {
        $table->dropColumn('customer_status_comment');
    });
}

};
