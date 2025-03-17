<?php

namespace Database\Factories;

use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttendanceRecord>
 */
class AttendanceRecordFactory extends Factory
{
    public function definition(): array
    {
        $date = now()->format('Y-m-d');
        $clockIn = Carbon::parse($date)->setTime(
            $this->faker->numberBetween(8, 10),
            $this->faker->numberBetween(0, 59)
        );

        return [
            'user_id' => User::factory(),
            'date' => $date,
            'clock_in' => $clockIn->format('Y-m-d H:i:s'),
            'work_status' => AttendanceRecord::STATUS_FINISHED_WORK,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function withDate(string $date): self
    {
        $clockIn = Carbon::parse($date)->setTime(
            $this->faker->numberBetween(8, 10),
            $this->faker->numberBetween(0, 59)
        );
        $clockOut = (clone $clockIn)->addHours(8);

        return $this->state([
            'date' => $date,
            'clock_in' => $clockIn->format('Y-m-d H:i:s'),
            'clock_out' => $clockOut->format('Y-m-d H:i:s'),
            'total_work_minutes' => $clockIn->diffInMinutes($clockOut),
        ]);
    }
}
