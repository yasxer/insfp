<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * A "rattrapage" exam is a second chance on the module's examen, open only to
 * students whose semester average is below 10, in the modules they failed.
 * Its mark replaces the examen mark when it is higher.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE exams MODIFY exam_type ENUM('controle', 'examen', 'rattrapage') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("UPDATE exams SET exam_type = 'examen' WHERE exam_type = 'rattrapage'");
        DB::statement("ALTER TABLE exams MODIFY exam_type ENUM('controle', 'examen') NOT NULL");
    }
};
