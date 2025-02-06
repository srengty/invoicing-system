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
        Schema::create('customer_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name_khmer');
            $table->string('category_name_english');
            $table->string('description_khmer');
            $table->string('description_english');
            $table->timestamps();
        });
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->foreignId('customer_category_id')->nullable()->constrained('customer_categories')->nullOnDelete();
            $table->integer('code')->nullable();
            $table->integer('credit_period')->default(0)->comment('credit period in days');
            $table->string('address', 255)->nullable();
            $table->string('website', 255)->nullable();
            // ------------------------------------------
            $table->string('contact_person', 100)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('telegram_number', 20)->nullable();
            // ------------------------------------------
            $table->string('bank_name', 255)->nullable();
            $table->string('bank_address', 255)->nullable();
            $table->string('bank_account_name', 255)->nullable();
            $table->string('bank_account_number', 255)->nullable();
            $table->string('bank_swift', 255)->nullable();
            $table->timestamps();
        });
        Schema::create('quotations', function (Blueprint $table) {
            // $table->unsignedBigInteger('quotation_no')->primary()->comment('25000001:year 2025, quotation no 1');
            $table->unsignedBigInteger('quotation_no')->primary();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->timestamp('quotation_date')->useCurrent();
            $table->string('address', 255)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('terms', 255)->nullable();
            $table->double('total')->default(0)->comment('no tax');
            $table->string('status', 20)->default('Pending');
            $table->timestamps();
        });
        Schema::create('agreements', function (Blueprint $table) {
            $table->foreignId('quotation_no')->nullable()->constrained('quotations','quotation_no')->nullOnDelete();
            $table->unsignedBigInteger('agreement_no')->primary()->comment('25000001:year 2025, agreement no 1');
            $table->string('agreement_ref_no')->nullable()->comment('When Agreement is prepared by Customer');
            $table->timestamp('agreement_date')->useCurrent();
            $table->string('address', 255)->nullable();
            $table->string('agreement_doc', 255)->nullable()->comment('agreement document in PDF');
            $table->json('attachments')->comment('attachment files');
            // ------- Agreement Summary ------------
            $table->timestamp('start_date')->useCurrent();
            $table->timestamp('end_date')->useCurrent();
            $table->double('amount')->default(0)->comment('amount exclude tax');
            $table->string('status', 20)->default('Open')->comment('Open, Closed, Abnormal Closed');
            $table->string('short_description', 255)->nullable();
            $table->string('close_reason', 255)->nullable()->comment('Abnormal Closed reason');
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
