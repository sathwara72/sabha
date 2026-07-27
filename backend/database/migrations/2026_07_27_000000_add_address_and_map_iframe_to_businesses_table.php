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
            if (!Schema::hasColumn('businesses', 'address')) {
                $table->text('address')->nullable()->after('location');
            }
            if (!Schema::hasColumn('businesses', 'area')) {
                $table->string('area')->nullable()->after('address');
            }
            if (!Schema::hasColumn('businesses', 'city')) {
                $table->string('city')->nullable()->after('area');
            }
            if (!Schema::hasColumn('businesses', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
            if (!Schema::hasColumn('businesses', 'pincode')) {
                $table->string('pincode')->nullable()->after('state');
            }
            if (!Schema::hasColumn('businesses', 'map_iframe')) {
                $table->text('map_iframe')->nullable()->after('pincode');
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
                'address',
                'area',
                'city',
                'state',
                'pincode',
                'map_iframe',
            ]);
        });
    }
};
