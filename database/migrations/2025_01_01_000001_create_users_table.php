<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email', 255)->unique()->nullable();
            $table->string('phone', 20)->unique()->nullable();
            $table->string('password', 255);
            $table->enum('role', ['student', 'teacher', 'administration']);
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            $table->index(['role', 'is_approved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
