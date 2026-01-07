<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, convert to VARCHAR to allow value changes
        DB::statement("ALTER TABLE schedules MODIFY COLUMN study_mode VARCHAR(50) DEFAULT 'initial'");

        // Update existing values to new enum values
        DB::table('schedules')->where('study_mode', 'presencial')->update(['study_mode' => 'initial']);
        DB::table('schedules')->where('study_mode', 'apprentissage')->update(['study_mode' => 'alternance']);
        DB::table('schedules')->where('study_mode', 'cours_de_soir')->update(['study_mode' => 'continue']);

        // Then convert back to enum with new values
        DB::statement("ALTER TABLE schedules MODIFY COLUMN study_mode ENUM('initial', 'alternance', 'continue') DEFAULT 'initial'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert to VARCHAR first
        DB::statement("ALTER TABLE schedules MODIFY COLUMN study_mode VARCHAR(50) DEFAULT 'presencial'");

        // Revert values
        DB::table('schedules')->where('study_mode', 'initial')->update(['study_mode' => 'presencial']);
        DB::table('schedules')->where('study_mode', 'alternance')->update(['study_mode' => 'apprentissage']);
        DB::table('schedules')->where('study_mode', 'continue')->update(['study_mode' => 'cours_de_soir']);

        // Convert back to old enum
        DB::statement("ALTER TABLE schedules MODIFY COLUMN study_mode ENUM('presencial', 'apprentissage', 'cours_de_soir') DEFAULT 'presencial'");
    }
};
