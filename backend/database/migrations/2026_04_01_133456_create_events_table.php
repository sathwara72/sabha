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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->date('date');
            $table->time('time');
            $table->enum('type', ['physical', 'virtual', 'hybrid'])->default('physical');
            $table->enum('status', ['upcoming', 'current', 'past'])->default('upcoming');
            $table->string('category')->index();
            $table->text('description')->nullable();
            $table->string('attendees')->nullable();
            $table->string('price')->default('Free');
            $table->string('image_url')->nullable();
            $table->string('location_details')->nullable();
            $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
