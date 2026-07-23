<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->boolean('is_excluded')->default(false)->after('final_gpa');
            $table->timestamp('excluded_at')->nullable()->after('is_excluded');
            $table->string('exclusion_reason')->nullable()->after('excluded_at');
        });

        // One review row per student per (failed) semester cycle. The unique key
        // makes the whole advancement process idempotent — re-running an
        // activation cannot create duplicate reviews for the same cycle.
        Schema::create('advancement_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('student_id');
            $table->tinyInteger('semester');            // the semester the student failed
            $table->string('academic_year', 9);
            $table->decimal('average', 4, 2)->nullable();
            $table->enum('status', ['pending', 'redoubled', 'advanced', 'excluded'])->default('pending');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->unique(['student_id', 'semester', 'academic_year']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advancement_reviews');

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['is_excluded', 'excluded_at', 'exclusion_reason']);
        });
    }
};
