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
            if (!Schema::hasColumn('businesses', 'logo')) {
                $table->string('logo')->nullable()->after('name');
            }
            if (!Schema::hasColumn('businesses', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('logo');
            }
            if (!Schema::hasColumn('businesses', 'instagram')) {
                $table->string('instagram')->nullable()->after('linkedin');
            }
            if (!Schema::hasColumn('businesses', 'youtube')) {
                $table->string('youtube')->nullable()->after('instagram');
            }
            if (!Schema::hasColumn('businesses', 'twitter')) {
                $table->string('twitter')->nullable()->after('youtube');
            }
            if (!Schema::hasColumn('businesses', 'whatsapp')) {
                $table->string('whatsapp')->nullable()->after('twitter');
            }
            if (!Schema::hasColumn('businesses', 'services')) {
                $table->text('services')->nullable()->after('projects');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['logo', 'cover_image', 'instagram', 'youtube', 'twitter', 'whatsapp', 'services']);
        });
    }
};
