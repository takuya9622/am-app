<?php

namespace Database\Seeders;

use App\Models\BreakCorrection;
use App\Models\BreakRecord;
use Illuminate\Database\Seeder;

class BreakCorrectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BreakRecord::whereHas('attendanceRecord', function ($query) {
            $query->where('correction_request_status', 0);
        })->get()->each(function ($breakRecord) {
            BreakCorrection::factory()->create([
                'break_record_id' => $breakRecord->id,
            ]);
        });
    }
}