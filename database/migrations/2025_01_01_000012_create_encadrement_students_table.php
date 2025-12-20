<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encadrement_students', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('encadrement_group_id');
            $table->unsignedBigInteger('student_id');
            $table->timestamps();

            $table->foreign('encadrement_group_id')->references('id')->on('encadrement_groups')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');

            $table->unique(['encadrement_group_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encadrement_students');
    }
};
