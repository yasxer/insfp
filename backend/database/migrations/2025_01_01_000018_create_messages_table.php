<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('recipient_id')->nullable();
            $table->string('subject', 255);
            $table->text('body');
            $table->enum('recipient_type', ['all', 'students', 'teachers', 'administrations', 'specialty', 'individual'])->default('individual');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('recipient_id')->references('id')->on('users')->onDelete('cascade');

            $table->index(['recipient_id', 'is_read']);
            $table->index(['sender_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
