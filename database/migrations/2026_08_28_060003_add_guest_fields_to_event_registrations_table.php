<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        DB::statement('ALTER TABLE event_registrations MODIFY user_id BIGINT UNSIGNED NULL');

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->foreignId('purchased_by_user_id')->nullable()->after('user_id')
                ->constrained('users')->onDelete('set null');
            $table->string('guest_name')->nullable()->after('purchased_by_user_id');
            $table->string('guest_mobile')->nullable()->after('guest_name');
            $table->string('guest_email')->nullable()->after('guest_mobile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropForeign(['purchased_by_user_id']);
            $table->dropColumn(['purchased_by_user_id', 'guest_name', 'guest_mobile', 'guest_email']);
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        DB::statement('ALTER TABLE event_registrations MODIFY user_id BIGINT UNSIGNED NOT NULL');

        Schema::table('event_registrations', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
