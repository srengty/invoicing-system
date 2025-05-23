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
        DB::statement("ALTER TABLE payment_schedules MODIFY COLUMN status ENUM('UPCOMING', 'DUE SOON', 'PAST_DUE', 'PAID') DEFAULT 'UPCOMING'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE payment_schedules MODIFY COLUMN status ENUM('UPCOMING', 'PAST_DUE', 'PAID') DEFAULT 'UPCOMING'");
    }
};
