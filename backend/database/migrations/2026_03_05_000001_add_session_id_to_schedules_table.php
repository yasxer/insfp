<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('session_id')->nullable()->after('id');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('set null');
            $table->index(['session_id', 'specialty_id']);
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
            $table->dropIndex(['session_id', 'specialty_id']);
            $table->dropColumn('session_id');
        });
    }
};
