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
            $startDate = now()->subMonths(2)->startOfMonth();
            $endDate = now()->endOfMonth();

            while ($startDate <= $endDate) {
                $attendanceRecord = AttendanceRecord::factory()
                ->withDate($startDate->format('Y-m-d'))
                ->create(['user_id' => $user->id,]);

                BreakRecord::factory(2)->create([
                    'attendance_record_id' => $attendanceRecord->id,
                ]);

                $startDate->addDay();
            }
        });
    }
}
