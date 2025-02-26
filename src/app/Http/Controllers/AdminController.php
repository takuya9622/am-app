<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function staff()
    {
        $staff = User::all();

        return view('staff/staff-index', compact('staff'));
    }

    public function staffAttendance(Request $request, $staffId)
    {
        $isApproved = false;
        $currentMonth = $request->query('month', now()->format('Y/m'));
        $currentMonthStart = Carbon::createFromFormat('Y/m', $currentMonth)->startOfMonth();
        $currentMonthEnd = Carbon::createFromFormat('Y/m', $currentMonth)->endOfMonth();

        $staff = User::where('id', $staffId)->first();

        $attendanceRecords = AttendanceRecord::with(['user', 'breakRecords'])
        ->where('user_id', $staffId)
        ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
        ->get();

        $attendanceRecords->transform(function ($attendanceRecord) {
            $attendanceRecord->formatted_date = $attendanceRecord->date->isoFormat('MM/DD(ddd)');
            $attendanceRecord->formatted_clock_in = $attendanceRecord->clock_in->format('H:i');
            $attendanceRecord->formatted_clock_out = $attendanceRecord->clock_out
                ? $attendanceRecord->clock_out->format('H:i')
                : '';
            $attendanceRecord->formatted_break_time = $attendanceRecord->getFormattedBreakTime();
            $attendanceRecord->formatted_work_time = $attendanceRecord->getFormattedWorkTime();

            return $attendanceRecord;
        });

        $previousMonth = $currentMonthStart->copy()->subMonth()->format('Y/m');
        $nextMonth = $currentMonthStart->copy()->addMonth()->format('Y/m');

        $tab = null;

        return view('staff/list',compact('staff', 'currentMonth', 'attendanceRecords', 'previousMonth', 'nextMonth', 'isApproved'));
    }

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

        $thisMonth = Carbon::createFromFormat('Y/m/d', $todayFormatted);
        $yesterday = $thisMonth->copy()->subDay()->format('Y/m/d');
        $tomorrow = $thisMonth->copy()->addDay()->format('Y/m/d');

        $tab = null;
        $isApproved = false;

        return view('staff/list', compact('todayFormatted', 'attendanceRecords', 'yesterday', 'tomorrow', 'isApproved'));
    }
}
