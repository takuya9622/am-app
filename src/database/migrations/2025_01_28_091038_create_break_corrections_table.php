<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('break_corrections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('break_record_id')->constrained()->onDelete('cascade');
            // $table->foreignId('attendance_correction_id')->constrained('attendance_corrections')->onDelete('cascade');
            $table->datetime('correction_start_time')->nullable();
            $table->datetime('correction_end_time')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('break_corrections');
    }
};
