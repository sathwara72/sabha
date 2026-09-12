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
            if (!Schema::hasColumn('events', 'price_normal')) {
                $table->string('price_normal')->nullable()->after('image');
            }
            if (!Schema::hasColumn('events', 'price_verified')) {
                $table->string('price_verified')->nullable()->after('price_normal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['price_normal', 'price_verified']);
        });
    }
};
