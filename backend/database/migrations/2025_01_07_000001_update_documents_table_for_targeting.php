<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Target type: 'all_teachers', 'all_students', 'session_students', 'specialty_students'
            $table->string('target_type', 50)->default('all_students')->after('is_public');

            // Session ID (nullable, used when target_type is 'session_students' or 'specialty_students')
            $table->unsignedBigInteger('session_id')->nullable()->after('target_type');

            // JSON array of specialty IDs (used when target_type is 'specialty_students')
            $table->json('specialty_ids')->nullable()->after('session_id');

            // Add foreign key
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('set null');

            // Index for faster queries
            $table->index('target_type');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
            $table->dropColumn(['target_type', 'session_id', 'specialty_ids']);
        });
    }
};
