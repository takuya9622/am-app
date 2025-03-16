<?php

namespace Tests\Helpers;

use App\Models\AttendanceRecord;
use App\Models\BreakRecord;
use App\Models\User;
use Mockery;

trait CreatesTestData
{
    public function createTestUser()
    {
        return User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);
    }

    public function createAttendanceRecord($user, $status)
    {
        $date = now()->format('Y-m-d');
        $clockIn = now();

        return AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => $clockIn,
            'work_status' => $status,
        ]);
    }

    public function createMultipleAttendanceRecords($user, $previousMonths = false, $nextMonths = false)
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        if ($previousMonths === true) {
            $startDate = now()->subMonths()->startOfMonth();
            $endDate = now()->subMonths()->endOfMonth();
        } elseif ($nextMonths === true) {
            $startDate = now()->addMonths()->startOfMonth();
            $endDate = now()->addMonths()->endOfMonth();
        }
        $attendanceRecords = collect();

        while ($startDate <= $endDate) {
            $attendanceRecord = AttendanceRecord::factory()
                ->withDate($startDate->format('Y-m-d'))
                ->create([
                    'user_id' => $user->id,
                    'date' => $startDate->format('Y-m-d'),
                ]);

            BreakRecord::factory()->create([
                'attendance_record_id' => $attendanceRecord->id,
            ]);
            $attendanceRecords->push($attendanceRecord);

            $startDate->addDay();
        }

        return $attendanceRecords;
    }
}