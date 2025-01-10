<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TimeConversionTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BreakRecord extends Model
{
    use HasFactory, TimeConversionTrait;

    protected $fillable = [
        'attendance_record_id',
        'start_time',
        'end_time',
        'break_duration',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'break_duration' => 'integer',
        ];
    }

    public function attendanceRecord()
    {
        return $this->belongsTo(AttendanceRecord::class, 'attendance_record_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function calculateBrakeDuration(): int
    {
        return $this->start_time && $this->end_time
            ? $this->start_time->diffInMinutes($this->end_time)
            : 0;
    }
}
