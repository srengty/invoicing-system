<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->integer('code')->nullable();
            $table->string('address', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('telegram_number', 20)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('bank_name', 255)->nullable();
            $table->string('bank_address', 255)->nullable();
            $table->string('bank_account_name', 255)->nullable();
            $table->string('bank_account_number', 255)->nullable();
            $table->string('bank_swift', 255)->nullable();
            $table->timestamps();
        });
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->integer('quotation_no')->unique('quotation_no_unique');
            $table->timestamp('quotation_date')->useCurrent();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('address', 255)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('terms', 255)->nullable();
            $table->double('total')->default(0)->comment('no tax');
            $table->double('tax')->default(0);
            $table->double('grand_total')->default(0);
            $table->string('status', 20)->default('Pending');
            $table->string('customer_status', 20)->default('Pending');
            $table->timestamps();
        });
        Schema::create('agreements', function (Blueprint $table) {
            $table->id();
            $table->integer('agreement_no')->unique('quotation_no_unique');
            $table->string('agreement_ref_no')->nullable();
            $table->timestamp('agreement_date')->useCurrent();
            $table->string('address', 255)->nullable();
            $table->string('agreement_doc', 250)->nullable();
            $table->timestamp('start_date')->useCurrent();
            $table->timestamp('end_date')->useCurrent();
            $table->double('amount_no_tax')->default(0)->comment('amount exclude tax');
            $table->double('tax')->default(0)->comment('tax in amount $');
            $table->string('status', 20)->default('Open')->comment('Open, Closed, Abnormal Closed');
            $table->string('close_reason', 255)->nullable()->comment('Abnormal Closed reason');
            $table->foreignId('quotation_id')->nullable()->constrained('quotations')->nullOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->double('due_payment')->default(0)->comment('total amount invoice not paid');
            $table->double('total_progress_payment')->default(0)->comment('total amount invoice paid');
            $table->double('total_progress_payment_percentage')->default(0)->comment('percentage of total amount invoice paid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agreements');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('customers');
    }
};
