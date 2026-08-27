<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Defaulting to 'active' backfills every existing row automatically —
            // current members must never be locked out by this new gate.
            $table->string('registration_status')->default('active')->after('role');
            $table->string('ref1_name')->nullable()->after('registration_status');
            $table->string('ref1_phone')->nullable()->after('ref1_name');
            $table->string('ref2_name')->nullable()->after('ref1_phone');
            $table->string('ref2_phone')->nullable()->after('ref2_name');
            $table->string('aadhar_document')->nullable()->after('ref2_phone');
            $table->string('pan_document')->nullable()->after('aadhar_document');
            $table->string('business_document')->nullable()->after('pan_document');
            $table->string('business_document_type')->nullable()->after('business_document');
            $table->string('membership_payment_screenshot')->nullable()->after('business_document_type');
            $table->text('registration_rejection_reason')->nullable()->after('membership_payment_screenshot');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'registration_status',
                'ref1_name', 'ref1_phone', 'ref2_name', 'ref2_phone',
                'aadhar_document', 'pan_document', 'business_document', 'business_document_type',
                'membership_payment_screenshot', 'registration_rejection_reason',
            ]);
        });
    }
};
