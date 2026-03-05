<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedule_statuses', function (Blueprint $table) {
            // Drop FKs first (MySQL requires this before dropping the unique index they rely on)
            $table->dropForeign(['session_id']);
            $table->dropForeign(['specialty_id']);
            // Drop old unique constraint
            $table->dropUnique('schedule_status_unique');
            // Add study_mode column
            $table->string('study_mode', 20)->nullable()->after('semester');
            // Re-add FKs
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
            $table->foreign('specialty_id')->references('id')->on('specialties')->onDelete('cascade');
            // New unique constraint including study_mode
            $table->unique(
                ['session_id', 'specialty_id', 'semester', 'study_mode', 'group'],
                'schedule_status_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('schedule_statuses', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
            $table->dropForeign(['specialty_id']);
            $table->dropUnique('schedule_status_unique');
            $table->dropColumn('study_mode');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
            $table->foreign('specialty_id')->references('id')->on('specialties')->onDelete('cascade');
            $table->unique(
                ['session_id', 'specialty_id', 'semester', 'group'],
                'schedule_status_unique'
            );
        });
    }
};
