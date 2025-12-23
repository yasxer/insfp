<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specialties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->integer('duration_semesters')->default(6);
            $table->boolean('is_active')->default(true);
            $table->string('cover_image', 500)->nullable();
            $table->string('brochure_path', 500)->nullable();
            $table->string('brochure_name', 255)->nullable();
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specialties');
    }
};
