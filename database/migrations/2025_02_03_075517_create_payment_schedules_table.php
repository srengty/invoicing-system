<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agreement_no')->constrained('agreements','agreement_no')->onDelete('cascade');
            $table->date('due_date');
            $table->string('short_description');
            $table->decimal('percentage', 10, 2);
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->string('currency')->default('KHR');
            $table->enum('status', ['UPCOMING', 'PAST_DUE', 'PAID'])->default('UPCOMING');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_schedules');
    }
};
