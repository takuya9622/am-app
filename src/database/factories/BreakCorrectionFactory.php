<?php

namespace Database\Factories;

use App\Models\AttendanceRecord;
use App\Models\BreakRecord;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BreakCorrection>
 */
class BreakCorrectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('10:00:00', '12:30:00');
        $end = $this->faker->dateTimeBetween('13:00:00', '15:30:00');

        return [
            'break_record_id' => BreakRecord::inRandomOrder()->first()->id,
            'correction_start_time' => $start,
            'correction_end_time' => $end,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
