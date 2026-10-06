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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'zybra_party_id')) {
                $table->unsignedBigInteger('zybra_party_id')->nullable()->after('id');
            }
            if (! Schema::hasColumn('users', 'gstin')) {
                $table->string('gstin', 15)->nullable()->after('email');
            }
            if (! Schema::hasColumn('users', 'pan')) {
                $table->string('pan', 10)->nullable()->after('gstin');
            }
            if (! Schema::hasColumn('users', 'state_code')) {
                $table->string('state_code', 2)->default('24')->after('pan');
            }
            if (! Schema::hasColumn('users', 'pincode')) {
                $table->string('pincode', 10)->nullable()->after('city');
            }
            if (! Schema::hasColumn('users', 'zybra_membership_receipt_no')) {
                $table->string('zybra_membership_receipt_no', 50)->nullable()->after('registration_status');
            }
            if (! Schema::hasColumn('users', 'zybra_sync_status')) {
                $table->string('zybra_sync_status', 30)->default('pending')->after('zybra_membership_receipt_no');
            }
            if (! Schema::hasColumn('users', 'zybra_error')) {
                $table->text('zybra_error')->nullable()->after('zybra_sync_status');
            }
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('event_registrations', 'zybra_receipt_no')) {
                $table->string('zybra_receipt_no', 50)->nullable()->after('ticket_number');
            }
            if (! Schema::hasColumn('event_registrations', 'zybra_sync_status')) {
                $table->string('zybra_sync_status', 30)->default('pending')->after('zybra_receipt_no');
            }
            if (! Schema::hasColumn('event_registrations', 'zybra_error')) {
                $table->text('zybra_error')->nullable()->after('zybra_sync_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'zybra_party_id',
                'gstin',
                'pan',
                'state_code',
                'pincode',
                'zybra_membership_receipt_no',
                'zybra_sync_status',
                'zybra_error',
            ]);
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'zybra_receipt_no',
                'zybra_sync_status',
                'zybra_error',
            ]);
        });
    }
};
