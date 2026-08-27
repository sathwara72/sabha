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
            if (! Schema::hasColumn('events', 'youtube_url')) {
                $table->string('youtube_url')->nullable()->after('image');
            }
            if (! Schema::hasColumn('events', 'is_popup')) {
                $table->boolean('is_popup')->default(false)->after('youtube_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['youtube_url', 'is_popup']);
        });
    }
};
