<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('administration_id');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable();
            $table->string('file_path', 500);
            $table->string('file_name', 255);
            $table->boolean('is_public')->default(false);
            $table->date('valid_until')->nullable();
            $table->timestamps();

            $table->foreign('administration_id')->references('id')->on('administrations')->onDelete('cascade');

            $table->index(['category', 'is_public']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
