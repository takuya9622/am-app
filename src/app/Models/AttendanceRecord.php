<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TimeConversionTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceRecord extends Model
{
    use HasFactory, TimeConversionTrait;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'total_work_minutes',
        'work_status',
        'remarks',
        'correction_request_status',
    ];
    public function breakRecords()
    {
        return $this->hasMany(BreakRecord::class, 'attendance_record_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public const STATUS_OUTSIDE_WORK = 0;
    public const STATUS_AT_WORK = 1;
    public const STATUS_ON_BREAK = 2;
    public const STATUS_FINISHED_WORK = 3;

    public const WORK_STATUSES = [
        self::STATUS_OUTSIDE_WORK => '勤務外',
        self::STATUS_AT_WORK => '出勤中',
        self::STATUS_ON_BREAK => '休憩中',
        self::STATUS_FINISHED_WORK => '退勤済',
    ];

    public function getWorkStatusAttribute()
    {
        return self::WORK_STATUSES[$this->attributes['work_status']] ?? 'unknown';
    }

    public const STATUS_PENDING = 0;
    public const STATUS_APPROVED = 1;

    public const CORRECTION_STATUSES = [
        self::STATUS_PENDING => '承認待ち',
        self::STATUS_APPROVED => '承認済',
    ];

    public function scopePendingCorrection($query)
    {
        return $query->where('correction_request_status', self::STATUS_PENDING);
    }

    public function scopeApprovedCorrection($query)
    {
        return $query->where('correction_request_status', self::STATUS_APPROVED);
    }

    protected function casts(): array
    {
        return [
            'clock_in' => 'datetime',
            'clock_out' => 'datetime',
            'work_status' => 'integer',
            'correction_request_status' => 'integer',
        ];
    }

    public function calculateTotalWorkMinutes(): int
    {
        return $this->clock_in && $this->clock_out
            ? $this->clock_in->diffInMinutes($this->clock_out)
            : 0;
    }

    public function getTotalWorkHoursAndMinutes(): array
    {
        $totalMinutes = $this->calculateTotalWorkMinutes();
        return $this->convertMinutesToHoursAndMinutes($totalMinutes);
    }

    public function calculateTotalBreakMinutes(): int
    {
        return $this->breakRecords->sum->calculateDuration();
    }


    public function getTotalBreakHoursAndMinutes(): array
    {
        $totalMinutes = $this->calculateTotalBreakMinutes();
        return $this->convertMinutesToHoursAndMinutes($totalMinutes);
    }
}