<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AttendanceTable extends Component
{
    public $attendanceRecords;
    public $isApproved;

    public function __construct($attendanceRecords, $isApproved = false)
    {
        $this->attendanceRecords = $attendanceRecords;
        $this->isApproved = ($isApproved ?? false);
    }

    public function render(): View|Closure|string
    {
        return view('components.attendance-table');
    }
}
