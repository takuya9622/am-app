<?php

namespace Database\Seeders;

use App\Models\BreakCorrection;
use App\Models\BreakRecord;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BreakCorrectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $breakRecordIds = BreakRecord::orderBy('id')->pluck('id')->toArray();
        static $index = 0;

        BreakCorrection::factory(120)->create()->each(function ($post) use ($breakRecordIds, &$index) {
            $post->update(['break_record_id' => $breakRecordIds[$index % count($breakRecordIds)]]);
            $index += 5;
        });
    }
}
