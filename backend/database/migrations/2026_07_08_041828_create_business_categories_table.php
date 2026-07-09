<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default categories
        $categories = [
            'Software Development',
            'Supply Chain',
            'Digital Marketing',
            'Construction',
            'Financial Services',
            'Renewables',
            'Creative Agency',
            'Venture Capital',
            'Healthcare',
            'Education',
            'Real Estate',
            'Manufacturing',
            'Retail',
            'Logistics',
            'Consulting',
        ];

        foreach ($categories as $i => $name) {
            \DB::table('business_categories')->insert([
                'name' => $name,
                'sort_order' => $i,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('business_categories');
    }
};
