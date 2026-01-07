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
        // Fix day enum to include sunday and saturday, exclude friday
        DB::statement("ALTER TABLE schedules MODIFY COLUMN day ENUM('saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE schedules MODIFY COLUMN day ENUM('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday') NOT NULL");
    }
};
