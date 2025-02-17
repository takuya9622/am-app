<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\TimeConversionTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceCorrection extends Model
{
    use HasFactory, TimeConversionTrait;

    protected $fillable = [
        'attendance_record_id',
        'correction_date',
        'correction_clock_in',
        'correction_clock_out',
        'correction_remarks',
        'correction_request_status',
    ];

    protected function casts(): array
    {
        return [
            'correction_date' => 'date',
            'correction_clock_in' => 'datetime',
            'correction_clock_out' => 'datetime',
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

    public function scopeApprovedCorrection($query)
    {
        return $query->where('correction_request_status', self::STATUS_APPROVED);
    }
}
