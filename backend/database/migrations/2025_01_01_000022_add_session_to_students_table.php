<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedBigInteger('session_specialty_id')->nullable()->after('specialty_id');

            $table->foreign('session_specialty_id')
                  ->references('id')
                  ->on('session_specialties')
                  ->onDelete('set null');

            $table->index('session_specialty_id');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['session_specialty_id']);
            $table->dropColumn('session_specialty_id');
        });
    }
};
