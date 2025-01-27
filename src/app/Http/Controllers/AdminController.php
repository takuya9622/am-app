<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function list(Request $request)
    {
        $todayFormatted = $request->query('date', now()->format('Y/m/d'));

        $today = Carbon::createFromFormat('Y/m/d', $todayFormatted)->format('Y-m-d');

        $attendanceRecords = AttendanceRecord::with(['user', 'breakRecords'])
            ->where('date', $today)
            ->get();

        $attendanceRecords->transform(function ($attendanceRecord) {
            $attendanceRecord->staff_name = $attendanceRecord->user->name;
            $attendanceRecord->formatted_clock_in = $attendanceRecord->clock_in->format('H:i');
            $attendanceRecord->formatted_clock_out = $attendanceRecord->clock_out
                ? $attendanceRecord->clock_out->format('H:i')
                : '';
            $attendanceRecord->formatted_break_time = $attendanceRecord->getFormattedBreakTime();
            $attendanceRecord->formatted_work_time = $attendanceRecord->getFormattedWorkTime();

            return $attendanceRecord;
        });

        $ttttt = Carbon::createFromFormat('Y/m/d', $todayFormatted);
        $yesterday = $ttttt->copy()->subDay()->format('Y/m/d');
        $tomorrow = $ttttt->copy()->addDay()->format('Y/m/d');

        $tab = null;

        return view('staff/list', compact('todayFormatted', 'attendanceRecords', 'yesterday', 'tomorrow'));
    }
}
