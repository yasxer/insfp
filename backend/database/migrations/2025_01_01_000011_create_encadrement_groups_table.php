<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encadrement_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('specialty_id');
            $table->string('project_title', 255)->nullable();
            $table->text('project_description')->nullable();
            $table->string('academic_year', 9);
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->foreign('specialty_id')->references('id')->on('specialties')->onDelete('cascade');

            $table->index(['teacher_id', 'academic_year']);
            $table->index(['specialty_id', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encadrement_groups');
    }
};
