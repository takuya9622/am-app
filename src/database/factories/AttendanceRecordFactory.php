<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttendanceRecord>
 */
class AttendanceRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $clockIn = Carbon::instance($this->faker->dateTimeBetween('08:00:00', '10:00:00'));
        $clockOut = (clone $clockIn)->addHours(8);
        return [
            'user_id' => User::factory(),
            'date' => $this->faker->dateTimeThisMonth->format('Y-m-d'),
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'total_work_minutes' => $clockIn->diffInMinutes($clockOut),
            'work_status' => 3,
            'remarks' => $this->faker->sentence,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
