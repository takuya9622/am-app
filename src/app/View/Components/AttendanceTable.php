<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AttendanceTable extends Component
{
    public $attendanceRecords;

    public function __construct($attendanceRecords)
    {
        $this->attendanceRecords = $attendanceRecords;
    }

    public function render(): View|Closure|string
    {
        return view('components.attendance-table');
    }
}
