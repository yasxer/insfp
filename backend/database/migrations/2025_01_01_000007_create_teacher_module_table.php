<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_module', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('module_id');
            $table->string('academic_year', 9);
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');

            $table->unique(['teacher_id', 'module_id', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_module');
    }
};
