<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('number', 50)->unique();
            $table->boolean('is_used')->default(false);
            $table->foreignId('specialty_id')->constrained()->onDelete('cascade');
            $table->string('academic_year', 9);
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index(['number', 'is_used']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_numbers');
    }
};
