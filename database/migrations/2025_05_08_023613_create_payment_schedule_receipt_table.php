<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_schedule_receipt', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_schedule_id');
            $table->unsignedBigInteger('receipt_receipt_no'); // ✅ matches receipts.receipt_no
            $table->timestamps();
        
            $table->primary(['payment_schedule_id', 'receipt_receipt_no']);
        
            $table->foreign('payment_schedule_id')
                ->references('id')
                ->on('payment_schedules')
                ->onDelete('cascade');
        
            $table->foreign('receipt_receipt_no')
                ->references('receipt_no')
                ->on('receipts')
                ->onDelete('cascade');
        });
                                            
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_schedule_receipt');
    }
};
