<?php

namespace Database\Seeders;

use App\Models\AttendanceCorrection;
use App\Models\AttendanceRecord;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceCorrectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attendanceRecordIds = AttendanceRecord::orderBy('id')->pluck('id')->toArray();
        static $index = 0;

        AttendanceCorrection::factory(120)->create()->each(function ($post) use ($attendanceRecordIds, &$index) {
            $post->update(['attendance_record_id' => $attendanceRecordIds[$index % count($attendanceRecordIds)]]);
            $index += 3;
        });
    }
}
