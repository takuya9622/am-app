<?php

namespace Database\Factories;

use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttendanceCorrection>
 */
class AttendanceCorrectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $attendanceRecord = AttendanceRecord::inRandomOrder()->first();
        $date = $attendanceRecord->date;
        $clockIn = Carbon::parse($date)->setTime(
            $this->faker->numberBetween(8, 10),
            $this->faker->numberBetween(0, 59)
        );
        $clockOut = (clone $clockIn)->addHours(8);

        return [
            'attendance_record_id' => $attendanceRecord->id,
            'correction_date' => $date,
            'correction_clock_in' => $clockIn->format('Y-m-d H:i:s'),
            'correction_clock_out' => $clockOut->format('Y-m-d H:i:s'),
            'remarks' => "電車遅延のため",
        ];
    }
}
