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
        Schema::table('hero_images', function (Blueprint $table) {
            $table->string('link_type')->nullable()->after('caption'); // 'event' | 'external'
            $table->foreignId('event_id')->nullable()->after('link_type')->constrained('events')->nullOnDelete();
            $table->string('external_url')->nullable()->after('event_id');
            $table->integer('sort_order')->default(0)->after('external_url');
        });

        // Backfill sort_order from existing creation order so current slides
        // keep their relative position instead of all collapsing to 0.
        DB::table('hero_images')->orderBy('created_at')->get(['id'])->each(function ($row, $index) {
            DB::table('hero_images')->where('id', $row->id)->update(['sort_order' => $index]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hero_images', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropColumn(['link_type', 'event_id', 'external_url', 'sort_order']);
        });
    }
};
