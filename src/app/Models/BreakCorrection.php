<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\TimeConversionTrait;

class BreakCorrection extends Model
{
    use HasFactory, TimeConversionTrait;
    public function breakRecord()
    {
        return $this->belongsTo(BreakRecord::class, 'break_record_id');
    }
}
