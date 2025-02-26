<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\TimeConversionTrait;

class BreakCorrection extends Model
{
    use HasFactory, TimeConversionTrait;

    protected $fillable = [
        'break_record_id',
        'correction_start_time',
        'correction_end_time',
    ];

    protected function casts(): array
    {
        return [
            'correction_start_time' => 'datetime',
            'correction_end_time' => 'datetime',
        ];
    }
    public function breakRecord()
    {
        return $this->belongsTo(BreakRecord::class, 'break_record_id');
    }
}
