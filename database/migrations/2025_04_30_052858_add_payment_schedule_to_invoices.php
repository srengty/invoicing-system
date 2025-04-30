<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_schedule_id')->nullable(); // Add nullable foreign key column
            $table->foreign('payment_schedule_id')->references('id')->on('payment_schedules')->onDelete('set null'); // Foreign key referencing 'payment_schedules'
        });
    }
    
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['payment_schedule_id']); // Remove the foreign key constraint
            $table->dropColumn('payment_schedule_id'); // Drop the column
        });
    }
    
};
