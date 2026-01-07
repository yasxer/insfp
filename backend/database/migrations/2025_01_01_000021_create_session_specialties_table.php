<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_specialties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('specialty_id');
            $table->enum('study_type', ['presential', 'apprentissage', 'cours_soir']);
            $table->timestamps();

            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
            $table->foreign('specialty_id')->references('id')->on('specialties')->onDelete('restrict');

            // Same specialty can't be repeated in same study_type within a session
            $table->unique(['session_id', 'specialty_id', 'study_type'], 'unique_session_specialty_type');

            $table->index(['session_id', 'study_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_specialties');
    }
};
