<?php

namespace App\Traits;

trait TimeConversionTrait
{
    public function convertMinutesToHoursAndMinutes(int $totalMinutes): string
    {
        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }
}
