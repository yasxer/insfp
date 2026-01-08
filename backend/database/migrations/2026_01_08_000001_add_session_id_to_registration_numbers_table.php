<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_numbers', function (Blueprint $table) {
            $table->foreignId('session_id')->nullable()->after('specialty_id')->constrained('sessions')->onDelete('cascade');
            // Allow academic_year to be nullable if we are relying on session, or keep it as redundant info.
            // The request says "replace academic years in db ... by session", but keeping it nullable is safer for existing data if any.
            $table->string('academic_year', 9)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('registration_numbers', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
            $table->dropColumn('session_id');
            $table->string('academic_year', 9)->nullable(false)->change();
        });
    }
};
