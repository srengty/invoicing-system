<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->decimal('installment_paid', 15, 2)->nullable()->after('paid_amount');
        });
    }

    public function down()
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn('installment_paid');
        });
    }

};
