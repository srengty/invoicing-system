<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerStatusToInvoicesTable extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Add the new columns
            $table->string('customer_status')->nullable(); // customer status
            $table->text('customer_status_comment')->nullable(); // customer status comment
            $table->string('payment_status')->nullable(); // payment status
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Remove the columns in case of rollback
            $table->dropColumn('customer_status');
            $table->dropColumn('customer_status_comment');
            $table->dropColumn('payment_status');
        });
    }
}
