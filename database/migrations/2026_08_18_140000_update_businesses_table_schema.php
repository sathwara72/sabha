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
            if (!Schema::hasColumn('businesses', 'designation')) {
                $table->text('designation')->nullable()->after('name');
            }
            if (!Schema::hasColumn('businesses', 'business_phone')) {
                if (Schema::hasColumn('businesses', 'phone')) {
                    $table->renameColumn('phone', 'business_phone');
                } else {
                    $table->string('business_phone')->nullable();
                }
            }
            if (!Schema::hasColumn('businesses', 'business_email')) {
                if (Schema::hasColumn('businesses', 'email')) {
                    $table->renameColumn('email', 'business_email');
                } else {
                    $table->string('business_email')->nullable();
                }
            }

            $columnsToDrop = [];
            if (Schema::hasColumn('businesses', 'location')) {
                $columnsToDrop[] = 'location';
            }
            if (Schema::hasColumn('businesses', 'city')) {
                $columnsToDrop[] = 'city';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            if (Schema::hasColumn('businesses', 'designation')) {
                $table->dropColumn('designation');
            }
            if (Schema::hasColumn('businesses', 'business_phone')) {
                $table->renameColumn('business_phone', 'phone');
            }
            if (Schema::hasColumn('businesses', 'business_email')) {
                $table->renameColumn('business_email', 'email');
            }
            $table->string('location')->nullable();
            $table->string('city')->nullable();
        });
    }
};
