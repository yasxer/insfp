<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('specialty_id');
            $table->string('name', 255);
            $table->string('code', 50);
            $table->text('description')->nullable();
            $table->integer('semester');
            $table->decimal('coefficient', 3, 1)->default(1.0);
            $table->integer('hours_per_week')->default(1);
            $table->timestamps();

            $table->foreign('specialty_id')->references('id')->on('specialties')->onDelete('cascade');

            $table->unique(['specialty_id', 'code']);
            $table->index(['specialty_id', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
