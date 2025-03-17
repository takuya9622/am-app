<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BreakRecord>
 */
class BreakRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $start = Carbon::instance($this->faker->dateTimeBetween('10:00:00', '15:30:00'));
        $end = (clone $start)->modify('+30 minutes');
        return [
            //'attendance_record_id' => AttendanceRecord::factory(),
            'start_time' => $start,
            'end_time' => $end,
            'break_duration' => $start->diffInMinutes($end),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}