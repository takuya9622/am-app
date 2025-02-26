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
        'remarks',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'attendance_record_id' => 'integer',
            'correction_date' => 'date',
            'correction_clock_in' => 'datetime',
            'correction_clock_out' => 'datetime',
            'status' => 'integer',
        ];
    }

    const STATUS_APPROVED = 1;
    const STATUS_PENDING = 0;

    public const REQUEST_STATUS = [
        self::STATUS_APPROVED => 'approved',
        self::STATUS_PENDING => 'pending',
    ];

    public function getRequestStatus()
    {
        return self::REQUEST_STATUS[$this->attributes['status']] ?? 'unknown';
    }

    public function scopeApprovedCorrection($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function attendanceRecord()
    {
        return $this->belongsTo(AttendanceRecord::class, 'attendance_record_id');
    }
}
