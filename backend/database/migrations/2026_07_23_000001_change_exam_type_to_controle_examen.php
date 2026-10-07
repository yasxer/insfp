<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The institute only runs two kinds of assessment: "contrôle" and "examen".
 * (Rattrapage is not an exam type — it is derived from the semester average.)
 * This replaces the old midterm/final/rattrapage enum, remapping any existing
 * rows: midterm -> controle, final/rattrapage -> examen.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Widen to a plain string so the old values can be remapped safely.
        DB::statement("ALTER TABLE exams MODIFY exam_type VARCHAR(20) NOT NULL");

        DB::statement("UPDATE exams SET exam_type = 'controle' WHERE exam_type = 'midterm'");
        DB::statement("UPDATE exams SET exam_type = 'examen' WHERE exam_type IN ('final', 'rattrapage')");

        DB::statement("ALTER TABLE exams MODIFY exam_type ENUM('controle', 'examen') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE exams MODIFY exam_type VARCHAR(20) NOT NULL");

        DB::statement("UPDATE exams SET exam_type = 'midterm' WHERE exam_type = 'controle'");
        DB::statement("UPDATE exams SET exam_type = 'final' WHERE exam_type = 'examen'");

        DB::statement("ALTER TABLE exams MODIFY exam_type ENUM('midterm', 'final', 'rattrapage') NOT NULL");
    }
};
