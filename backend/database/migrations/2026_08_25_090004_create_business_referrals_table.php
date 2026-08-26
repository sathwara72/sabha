<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('giver_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->string('contact_name');
            $table->string('contact_number');
            $table->string('company_details')->nullable();
            $table->text('business_requirement');
            $table->string('lead_rating')->default('warm');
            $table->text('giver_comments')->nullable();
            $table->string('contact_status')->default('not_connected');
            $table->string('status')->default('pending');
            $table->decimal('amount', 10, 2)->nullable();
            $table->text('receiver_comments')->nullable();
            $table->text('testimonial')->nullable();
            $table->boolean('display_testimonial')->default(false);
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_referrals');
    }
};
