<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_corrections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_record_id')->constrained()->onDelete('cascade');
            $table->date('correction_date')->nullable();
            $table->datetime('correction_clock_in')->nullable();
            $table->datetime('correction_clock_out')->nullable();
            $table->text('correction_remarks')->nullable();
            $table->tinyInteger('correction_request_status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_corrections');
    }
};
