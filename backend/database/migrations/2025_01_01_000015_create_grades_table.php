<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('exam_id')->nullable();
            $table->decimal('grade', 4, 2);
            $table->integer('semester');
            $table->string('academic_year', 9);
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('set null');

            $table->unique(['student_id', 'module_id', 'exam_id']);
            $table->index(['student_id', 'semester', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
