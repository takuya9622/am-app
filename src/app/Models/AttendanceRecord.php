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
        'correction_request_status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'clock_in' => 'datetime',
            'clock_out' => 'datetime',
            'total_work_minutes' => 'integer',
            'work_status' => 'integer',
        ];
    }

    public const STATUS_PENDING = 0;
    public const STATUS_APPROVED = 1;

    public const CORRECTION_STATUSES = [
        self::STATUS_PENDING => '承認待ち',
        self::STATUS_APPROVED => '承認済み',
    ];

    public function getCorrectionRequestStatusAttribute()
    {
        return self::CORRECTION_STATUSES[$this->attributes['correction_request_status']] ?? 'unknown';
    }

    public function scopePendingCorrection($query)
    {
        return $query->where('correction_request_status', self::STATUS_PENDING);
    }

    public function breakRecords()
    {
        return $this->hasMany(BreakRecord::class, 'attendance_record_id');
    }

    public function attendanceCorrections()
    {
        return $this->hasMany(AttendanceCorrection::class, 'attendance_record_id');
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

    public function calculateTotalWorkMinutes(): int
    {
        if (!$this->clock_in || !$this->clock_out) {
            return 0;
        }

        $workMinutes = $this->clock_in->diffInMinutes($this->clock_out);
        $breakMinutes = $this->calculateTotalBreakMinutes();

        return $workMinutes - $breakMinutes;
    }

    public function getFormattedWorkTime(): string
    {
        $totalWorkMinutes = $this->total_work_minutes;
        if (is_null($totalWorkMinutes)) {
            return '';
        }

        return $this->convertMinutesToHoursAndMinutes($totalWorkMinutes);
    }

    public function getClockInAttribute($value)
    {
        return Carbon::parse($value)->setSeconds(0);
    }

    public function getClockOutAttribute($value)
    {
        return Carbon::parse($value)->setSeconds(0);
    }


    public function calculateTotalBreakMinutes(): int
    {
        return $this->breakRecords()->sum('break_duration');
    }


    public function getFormattedBreakTime(): string
    {
        $totalBreakMinutes = $this->calculateTotalBreakMinutes();
        if ($totalBreakMinutes === 0) return '';

        return $this->convertMinutesToHoursAndMinutes($totalBreakMinutes);
    }

}