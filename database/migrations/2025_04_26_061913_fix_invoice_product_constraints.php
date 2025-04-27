<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixInvoiceProductConstraints extends Migration
{
    public function up(): void
    {
        // Find the foreign key name (you can replace this with the actual foreign key constraint name)
        $foreignKeyName = 'invoice_product_invoice_no_foreign';  // Replace this with your actual foreign key name

        // Drop the foreign key constraint first
        DB::statement("ALTER TABLE invoice_product DROP FOREIGN KEY $foreignKeyName");

        // Drop the unique index on invoice_no (as it's used by the foreign key constraint)
        DB::statement('ALTER TABLE invoice_product DROP INDEX invoice_product_invoice_no_unique');

        // Add a new composite unique constraint for invoice_no and product_id
        DB::statement('ALTER TABLE invoice_product ADD UNIQUE INDEX invoice_product_unique (invoice_no, product_id)');
    }

    public function down(): void
    {
        // Reverse the changes if needed
        DB::statement('ALTER TABLE invoice_product DROP INDEX invoice_product_unique');

        // Add the old unique index back if needed (adjust the name accordingly)
        DB::statement('ALTER TABLE invoice_product ADD UNIQUE INDEX invoice_product_invoice_no_unique (invoice_no)');

        // Recreate the foreign key if needed (replace with the correct names)
        $foreignKeyName = 'invoice_product_invoice_no_foreign';  // Replace this with your actual foreign key name
        DB::statement("ALTER TABLE invoice_product ADD CONSTRAINT $foreignKeyName FOREIGN KEY (invoice_no) REFERENCES invoices(invoice_no)");
    }
}
