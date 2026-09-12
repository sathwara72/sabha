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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'native_city')) {
                $table->string('native_city')->nullable()->after('city');
            }
            if (!Schema::hasColumn('users', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('native_city');
            }
            if (!Schema::hasColumn('users', 'anniversary_date')) {
                $table->date('anniversary_date')->nullable()->after('birth_date');
            }
            if (!Schema::hasColumn('users', 'residence_address')) {
                $table->text('residence_address')->nullable()->after('anniversary_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'native_city',
                'birth_date',
                'anniversary_date',
                'residence_address',
            ]);
        });
    }
};
