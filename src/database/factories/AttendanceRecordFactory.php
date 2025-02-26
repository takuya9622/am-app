<?php

namespace Database\Factories;

use App\Models\AttendanceRecord;
use App\Models\User;
use App\Traits\FactoryHelpers;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttendanceRecord>
 */
class AttendanceRecordFactory extends Factory
{
    use FactoryHelpers;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = $this->faker->dateTimeThisMonth->format('Y-m-d');
        $clockIn = Carbon::parse($date)->setTime(
            $this->faker->numberBetween(8, 10),
            $this->faker->numberBetween(0, 59)
        );
        $clockOut = (clone $clockIn)->addHours(8);

        return [
            'user_id' => User::factory(),
            'date' => $date,
            'clock_in' => $clockIn->format('Y-m-d H:i:s'),
            'clock_out' => $clockOut->format('Y-m-d H:i:s'),
            'total_work_minutes' => $clockIn->diffInMinutes($clockOut),
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
