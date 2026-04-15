<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->string('title')->nullable()->after('id');
            $table->string('status')->default('draft')->after('exam_type'); // draft, submitted, modified
            $table->unsignedBigInteger('teacher_id')->nullable()->after('specialty_id');
            $table->string('group')->nullable()->after('semester');

            // Add foreign key
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn(['title', 'status', 'teacher_id', 'group']);
        });
    }
};
