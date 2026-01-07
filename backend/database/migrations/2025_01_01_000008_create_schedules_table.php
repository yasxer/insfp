<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('specialty_id');
            $table->enum('day', ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday']);
            $table->time('start_time');
            $table->string('classroom', 50)->nullable();
            $table->integer('semester');
            $table->string('academic_year', 9);
            $table->timestamps();

            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->foreign('specialty_id')->references('id')->on('specialties')->onDelete('cascade');

            $table->index(['specialty_id', 'semester', 'day']);
            $table->index(['teacher_id', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
