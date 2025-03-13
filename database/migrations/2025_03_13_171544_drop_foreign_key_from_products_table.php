<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    // Drop the foreign key constraint only
    Schema::table('products', function (Blueprint $table) {
        $table->dropForeign(['division_id']);
    });
}

public function down()
{
    // Reverse the changes (add the foreign key constraint back)
    Schema::table('products', function (Blueprint $table) {
        $table->foreign('division_id')->references('id')->on('divisions');
    });
}

};
