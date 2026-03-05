<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('specialty_id');
            $table->integer('semester');
            $table->string('group', 10)->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
            $table->foreign('specialty_id')->references('id')->on('specialties')->onDelete('cascade');
            $table->unique(['session_id', 'specialty_id', 'semester', 'group'], 'schedule_status_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_statuses');
    }
};
