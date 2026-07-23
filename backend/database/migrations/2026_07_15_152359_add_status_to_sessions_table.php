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
        Schema::table('sessions', function (Blueprint $table) {
            $table->enum('status', ['pending', 'active', 'archived'])->default('pending')->after('is_active');
        });

        DB::table('sessions')->where('is_active', true)->update(['status' => 'active']);
        // A non-active session only counts as "pending" (awaiting activation) if it
        // never started yet. Anything whose month has already begun was either a
        // past cohort or got superseded by a newer active session — archive it.
        DB::table('sessions')->where('is_active', false)->where('start_date', '>', now())->update(['status' => 'pending']);
        DB::table('sessions')->where('is_active', false)->where('start_date', '<=', now())->update(['status' => 'archived']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
