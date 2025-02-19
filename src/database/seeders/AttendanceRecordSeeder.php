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
            foreach (range(1, 30) as $day) {
                $attendanceRecord = AttendanceRecord::factory()->create([
                    'user_id' => $user->id,
                    'date' => now()->startOfMonth()->addDays($day - 1)->format('Y-m-d'),
                ]);

                BreakRecord::factory(2)->create([
                    'attendance_record_id' => $attendanceRecord->id,
                ]);
            }
        });
    }
}
