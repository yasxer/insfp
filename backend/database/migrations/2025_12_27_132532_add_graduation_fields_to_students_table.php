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
        Schema::table('students', function (Blueprint $table) {
            $table->integer('graduation_year')->nullable()->after('is_graduated');
            $table->integer('graduation_semester')->nullable()->after('graduation_year');
            $table->decimal('final_gpa', 4, 2)->nullable()->after('graduation_semester');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['graduation_year', 'graduation_semester', 'final_gpa']);
        });
    }
};
