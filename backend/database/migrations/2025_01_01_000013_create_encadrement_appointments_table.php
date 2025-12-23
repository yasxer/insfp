<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encadrement_appointments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('encadrement_group_id');
            $table->dateTime('appointment_date');
            $table->text('agenda')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->boolean('is_approved')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('encadrement_group_id')->references('id')->on('encadrement_groups')->onDelete('cascade');

            $table->index(['encadrement_group_id', 'appointment_date'], 'enc_appt_group_date_idx');
            $table->index(['appointment_date', 'status'], 'enc_appt_date_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encadrement_appointments');
    }
};
