<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Indexes for columns that every directory/admin listing filters or
     * orders by, none of which had one — safe to add now while the tables
     * are still small.
     */
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->index('status');
            $table->index('category');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->index('date');
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['category']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex(['date']);
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
    }
};
