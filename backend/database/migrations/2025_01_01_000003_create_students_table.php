<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->unique();
            $table->unsignedBigInteger('specialty_id');
            $table->string('registration_number', 50)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->enum('study_mode', ['initial', 'alternance', 'continue'])->default('initial');

            $table->integer('current_semester')->default(1);
            $table->integer('years_enrolled')->default(1);
            $table->boolean('is_graduated')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('specialty_id')->references('id')->on('specialties')->onDelete('restrict');

            $table->index(['specialty_id', 'current_semester']);
            $table->index('is_graduated');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
