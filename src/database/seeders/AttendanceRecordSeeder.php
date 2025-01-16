<?php

namespace Database\Seeders;

use App\Models\AttendanceRecord;
use App\Models\BreakRecord;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceRecordSeeder extends Seeder
{
    public function run(): void
    {
        User::all()->each(function ($user) {
            $startDate = now()->subMonth()->startOfMonth();
            $endDate = now()->addMonth()->endOfMonth();

            while ($startDate <= $endDate) {
                $attendanceRecord = AttendanceRecord::factory()->create([
                    'user_id' => $user->id,
                    'date' => $startDate->format('Y-m-d'),
                    'remarks' => '電車遅延のため',
                ]);

                BreakRecord::factory(2)->create([
                    'attendance_record_id' => $attendanceRecord->id,
                ]);

                $startDate->addDay();
            }
        });
    }
}
