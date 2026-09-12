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
        Schema::table('events', function (Blueprint $table) {
            if (! Schema::hasColumn('events', 'booking_start_date')) {
                $table->dateTime('booking_start_date')->nullable()->after('date');
            }
            if (! Schema::hasColumn('events', 'booking_end_date')) {
                $table->dateTime('booking_end_date')->nullable()->after('booking_start_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'booking_start_date')) {
                $table->dropColumn('booking_start_date');
            }
            if (Schema::hasColumn('events', 'booking_end_date')) {
                $table->dropColumn('booking_end_date');
            }
        });
    }
};
