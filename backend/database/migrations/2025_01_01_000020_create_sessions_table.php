<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100); // e.g., "Session Septembre 2024"
            $table->tinyInteger('month'); // 1-12
            $table->year('year'); // 2024, 2025, etc.
            $table->date('start_date');
            $table->date('end_date'); // Automatically calculated (+30 months)
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['month', 'year']); // Can't have two sessions in same month/year
            $table->index('is_active');
            $table->index('end_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
