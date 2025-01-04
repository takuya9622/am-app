<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TimeConversionTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BreakRecord extends Model
{
    use HasFactory, TimeConversionTrait;

    protected $fillable = [
        'start_time',
        'end_time',
        'total_break_minutes',
    ];
    public function attendanceRecord()
    {
        return $this->belongsTo(AttendanceRecord::class, 'attendance_record_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function calculateTotalBreakMinutes(): int
    {
        return $this->breaks()->sum(function ($break) {
            return $break->start_time && $break->end_time
                ? $break->start_time->diffInMinutes($break->end_time)
                : 0;
        });
    }

    public function getTotalBreakHoursAndMinutes(): array
    {
        $totalMinutes = $this->calculateTotalBreakMinutes();
        return $this->convertMinutesToHoursAndMinutes($totalMinutes);
    }
}
