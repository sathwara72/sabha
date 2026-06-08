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
            if (!Schema::hasColumn('businesses', 'tagline')) {
                $table->string('tagline')->nullable()->after('category');
            }
            if (!Schema::hasColumn('businesses', 'location')) {
                $table->string('location')->nullable()->after('tagline');
            }
            if (!Schema::hasColumn('businesses', 'phone')) {
                $table->string('phone')->nullable()->after('website');
            }
            if (!Schema::hasColumn('businesses', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('businesses', 'linkedin')) {
                $table->string('linkedin')->nullable()->after('email');
            }
            if (!Schema::hasColumn('businesses', 'hours')) {
                $table->string('hours')->nullable()->after('linkedin');
            }
            if (!Schema::hasColumn('businesses', 'founded')) {
                $table->string('founded')->nullable()->after('hours');
            }
            if (!Schema::hasColumn('businesses', 'team_size')) {
                $table->string('team_size')->nullable()->after('founded');
            }
            if (!Schema::hasColumn('businesses', 'projects')) {
                $table->string('projects')->nullable()->after('team_size');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn([
                'tagline',
                'location',
                'phone',
                'email',
                'linkedin',
                'hours',
                'founded',
                'team_size',
                'projects'
            ]);
        });
    }
};
