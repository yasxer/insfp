<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('specialty_id');
            $table->enum('exam_type', ['midterm', 'final', 'rattrapage']);
            $table->dateTime('exam_date');
            $table->integer('duration_minutes');
            $table->string('classroom', 50)->nullable();
            $table->integer('semester');
            $table->string('academic_year', 9);
            $table->timestamps();

            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
            $table->foreign('specialty_id')->references('id')->on('specialties')->onDelete('cascade');

            $table->index(['module_id', 'exam_type', 'academic_year']);
            $table->index(['specialty_id', 'exam_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
