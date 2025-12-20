<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliberations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('student_id');
            $table->integer('semester');
            $table->string('academic_year', 9);
            $table->decimal('average', 4, 2)->nullable();
            $table->enum('result', ['passed', 'failed', 'rattrapage']);
            $table->text('observations')->nullable();
            $table->date('deliberation_date')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');

            $table->unique(['student_id', 'semester', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliberations');
    }
};
