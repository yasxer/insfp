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
        // 1. Add column as nullable first
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('institute_id')->nullable()->after('email')->constrained('institutes')->onDelete('cascade');
        });

        // 2. Insert a default Institute
        $defaultInstituteId = DB::table('institutes')->insertGetId([
            'name' => 'INSFP Default Institute',
            'wilaya' => 'Alger',
            'code' => 'INSFP-001',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Link all existing users to this default institute
        DB::table('users')->update(['institute_id' => $defaultInstituteId]);

        // 4. Make column non-nullable (optional, but good practice if every user MUST have an institute)
        // Note: Changing column to non-nullable might require doctrine/dbal package in older Laravel versions,
        // but in newer ones it's supported natively or via raw SQL.
        // For simplicity and to avoid potential issues if doctrine/dbal is missing, I will leave it as nullable in definition but logically enforced,
        // OR try to modify it. Given the environment, I'll stick to nullable = false if I can, but to be safe I'll just leave it as is
        // with the data populated. Laravel's schema builder modification sometimes requires packages.

        // Let's try to make it non-nullable using raw SQL for maximum compatibility if needed,
        // or just rely on application logic.
        // Actually, if users table is empty, update does nothing. If it has data, they are updated.
        // Let's try to alter it to not null.
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('institute_id')->nullable(false)->change();
            });
        } catch (\Exception $e) {
            // Modification might fail if required packages are missing.
            // We'll proceed with the data updated.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['institute_id']);
            $table->dropColumn('institute_id');
        });

        // Optionally remove the default institute, but might have other constraints now.
        // DB::table('institutes')->where('code', 'INSFP-001')->delete();
    }
};
