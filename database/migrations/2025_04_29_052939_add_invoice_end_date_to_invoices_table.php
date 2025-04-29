<?php

// In the migration file (e.g., database/migrations/xxxx_xx_xx_add_invoice_end_date_to_invoices_table.php)
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceEndDateToInvoicesTable extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('invoice_end_date')->nullable();  // Add the new column
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('invoice_end_date');  // Rollback if needed
        });
    }
}
