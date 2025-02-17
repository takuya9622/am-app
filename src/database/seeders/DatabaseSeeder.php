<?php

namespace Database\Seeders;

use App\Models\AttendanceCorrectionRequest;
use App\Models\BreakCorrection;
use App\Models\User;
use Database\Factories\AttendanceCorrectionRequestFactory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AttendanceRecordSeeder::class,
            AttendanceCorrectionSeeder::class,
            BreakCorrectionSeeder::class,
        ]);
    }
}
