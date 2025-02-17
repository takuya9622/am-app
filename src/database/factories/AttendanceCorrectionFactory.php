<?php

namespace Database\Factories;

use App\Models\AttendanceCorrection;
use App\Models\AttendanceRecord;
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
        return [
            'attendance_record_id' => AttendanceRecord::inRandomOrder()->first()->id,
            'correction_date' => optional($this->faker->optional()->dateTimeThisMonth())->format('Y-m-d'),
            'correction_clock_in' => optional($this->faker->optional())->time(),
            'correction_clock_out' => optional($this->faker->optional())->time(),
            'correction_remarks' => $this->faker->optional()->randomElement(["電車遅延のため"]),
            'correction_request_status' => AttendanceCorrection::STATUS_PENDING,
        ];
    }
}
