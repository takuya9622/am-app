<?php

namespace App\Traits;

trait TimeConversionTrait
{
    public function convertMinutesToHoursAndMinutes(int $totalMinutes): array
    {
        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;
        return ['hours' => $hours, 'minutes' => $minutes];
    }
}
