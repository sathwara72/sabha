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
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('website'); // pending, approved, rejected
            $table->boolean('is_verified')->default(false)->after('status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('email'); // admin, user
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['status', 'is_verified']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role']);
        });
    }
};
