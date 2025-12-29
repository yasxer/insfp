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
        Schema::table('specialties', function (Blueprint $table) {
            $table->integer('current_semester')->default(1)->after('description');
            $table->decimal('duration_years', 3, 1)->default(2.5)->after('current_semester');
            $table->string('program_pdf_path')->nullable()->after('cover_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('specialties', function (Blueprint $table) {
            $table->dropColumn(['current_semester', 'duration_years', 'program_pdf_path']);
        });
    }
};
