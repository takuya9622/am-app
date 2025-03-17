<?php

namespace Database\Seeders;

use App\Models\AttendanceCorrection;
use App\Models\AttendanceRecord;
use App\Models\BreakRecord;
use App\Models\User;
use Illuminate\Database\Seeder;

class AttendanceRecordSeeder extends Seeder
{
    public function run(): void
    {
        User::all()->each(function ($user) {
            $startDate = now()->subMonths(2)->startOfMonth();
            $endDate = now()->endOfMonth();
            $counter = 0;

            while ($startDate <= $endDate) {
                $counter++;

                $attendanceRecord = AttendanceRecord::factory()
                ->withDate($startDate->format('Y-m-d'))
                ->create([
                    'user_id' => $user->id,
                    'correction_request_status' => ($counter % 10 === 1) ? 0 : null,
                ]);

                BreakRecord::factory(2)->create([
                    'attendance_record_id' => $attendanceRecord->id,
                ]);

                AttendanceRecord::where('id', $attendanceRecord->id)
                ->where('correction_request_status', 0)
                ->get()
                ->each(function ($record) {
                    AttendanceCorrection::factory()->create([
                        'attendance_record_id' => $record->id,
                        'correction_date' => $record->date,
                    ]);
                });

                $startDate->addDay();
            }
        });
    }
}
