<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('schedule_id');
            $table->unsignedBigInteger('teacher_id');
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('absent');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');

            $table->unique(['student_id', 'schedule_id', 'attendance_date']);
            $table->index(['student_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
