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
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date')->index();
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->integer('total_work_minutes')->nullable();
            $table->tinyInteger('work_status')->nullable();
            $table->text('remarks')->nullable();
            $table->tinyInteger('correction_request_status')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
