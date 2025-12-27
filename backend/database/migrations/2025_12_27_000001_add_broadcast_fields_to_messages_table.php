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
        Schema::table('messages', function (Blueprint $table) {
            // Make recipient_id nullable for broadcast messages
            $table->unsignedBigInteger('recipient_id')->nullable()->change();

            // Add fields for broadcast tracking
            $table->integer('recipient_count')->nullable()->after('is_read');
            $table->json('recipient_filter')->nullable()->after('recipient_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['recipient_count', 'recipient_filter']);
        });
    }
};
