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
        Schema::table('events', function (Blueprint $table) {
            if (! Schema::hasColumn('events', 'youtube_urls')) {
                $table->json('youtube_urls')->nullable()->after('image');
            }
        });

        DB::table('events')->whereNotNull('youtube_url')->where('youtube_url', '!=', '')
            ->orderBy('id')
            ->each(function ($event) {
                DB::table('events')->where('id', $event->id)->update([
                    'youtube_urls' => json_encode([$event->youtube_url]),
                ]);
            });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('youtube_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('youtube_url')->nullable()->after('image');
        });

        DB::table('events')->whereNotNull('youtube_urls')->orderBy('id')->each(function ($event) {
            $urls = json_decode($event->youtube_urls, true) ?: [];
            DB::table('events')->where('id', $event->id)->update([
                'youtube_url' => $urls[0] ?? null,
            ]);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('youtube_urls');
        });
    }
};
