<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Local audit log of every money entry Sabha posts to Zybra (Zybra stays
     * the books of record; this records who in Sabha added what).
     */
    public function up(): void
    {
        Schema::create('financial_entries', function (Blueprint $table) {
            $table->id();
            // income, expense, expense_by_member, settle_pay, settle_receive, membership, event
            $table->string('type', 30);
            $table->date('entry_date');
            $table->decimal('amount', 12, 2);
            $table->unsignedBigInteger('zybra_account_id')->nullable();
            $table->string('zybra_account_name')->nullable();
            $table->unsignedBigInteger('zybra_party_id')->nullable();
            $table->string('party_name')->nullable();
            $table->foreignId('member_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('zybra_voucher_type', 20);
            $table->unsignedBigInteger('zybra_voucher_id');
            $table->string('zybra_voucher_number', 50)->nullable();
            $table->string('reference')->nullable();
            $table->string('description', 500)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['zybra_voucher_type', 'zybra_voucher_id']);
            $table->index('entry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_entries');
    }
};
