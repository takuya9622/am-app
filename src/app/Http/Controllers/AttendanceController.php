<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\BreakRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AttendanceController
{
    private function getAttendanceRecord(User $user)
    {
        return $user->attendanceRecords()
            ->where('date', now()->toDateString())
            ->first();
    }

    public function index()
    {
        $now = now();
        $user = Auth::user();
        $attendanceRecord = $this->getAttendanceRecord($user);

        return view('staff/attendance', compact('now', 'attendanceRecord'));
    }

    public function store()
    {
        $userId = Auth::id();

        AttendanceRecord::firstOrCreate(
            [
                'user_id' => $userId,
                'date' => now()->toDateString(),
            ],
            [
                'user_id' => $userId,
                'date' => now()->toDateString(),
                'clock_in' => now(),
                'work_status' => AttendanceRecord::STATUS_AT_WORK,
            ]
        );

        return redirect()->route('attendance.index');
    }

    public function update(Request $request, AttendanceRecord $attendance)
    {
        $action = $request->input('action');

        if ($action === 'endWork') {
            $user = Auth::user();
            $attendanceRecord = $this->getAttendanceRecord($user);

            if ($attendanceRecord && $attendanceRecord->clock_in) {
                $clockOut = now();
                $attendanceRecord->clock_out = $clockOut;
                $totalWorkMinutes = $attendanceRecord->calculateTotalWorkMinutes();

                $attendanceRecord->update([
                    'clock_out' => $clockOut,
                    'work_status' => AttendanceRecord::STATUS_FINISHED_WORK,
                    'total_work_minutes' => $totalWorkMinutes,
                ]);
            }

            return redirect()->route('attendance.index');
        }

        if ($action === 'startBreak') {
            $existingBreak = $attendance->breakRecords()->whereNull('end_time')->first();

            if (!$existingBreak) {
                $attendance->breakRecords()->create([
                    'start_time' => now(),
                ]);
            }

            $attendance->update([
                'work_status' => AttendanceRecord::STATUS_ON_BREAK,
            ]);

            return redirect()->route('attendance.index');
        }

        if ($action === 'endBreak') {
            $ongoingBreak = $attendance->breakRecords()->whereNull('end_time')->first();

            if ($ongoingBreak) {
                $ongoingBreak->update([
                    'end_time' => now(),
                ]);

                $breakDuration = $ongoingBreak->calculateBrakeDuration();
                $ongoingBreak->update([
                    'break_duration' => $breakDuration,
                ]);
            }

            $attendance->update([
                'work_status' => AttendanceRecord::STATUS_AT_WORK,
            ]);

            return redirect()->route('attendance.index');
        }

        return redirect()->route('attendance.index');
    }

    public function list(Request $request)
    {
        $currentMonth = $request->query('month', now()->format('Y/m'));
        $currentMonthStart = Carbon::createFromFormat('Y/m', $currentMonth)->startOfMonth();
        $currentMonthEnd = Carbon::createFromFormat('Y/m', $currentMonth)->endOfMonth();

        $attendanceRecords = AttendanceRecord::with(['user', 'breakRecords'])
        ->where('user_id', Auth::id())
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

        return view('staff/list', compact('currentMonth', 'attendanceRecords', 'previousMonth', 'nextMonth'));
    }

    public function edit($attendanceId)
    {
        $userName = Auth::user()->name;
        $attendanceRecord = AttendanceRecord::with('breakRecords')->find($attendanceId);
        $correctionRequestStatus = $attendanceRecord->correction_request_status;

        $attendanceRecord->formatted_year = $attendanceRecord->date->isoFormat('Y年');
        $attendanceRecord->formatted_date = $attendanceRecord->date->isoFormat('M月D日');
        $attendanceRecord->formatted_clock_in = $attendanceRecord->clock_in->format('H:i');
        $attendanceRecord->formatted_clock_out = $attendanceRecord->clock_out
            ? $attendanceRecord->clock_out->format('H:i')
            : '';

        $attendanceRecord->breakRecords->transform(function ($breakRecord) {
            $breakRecord->formatted_break_start_time = $breakRecord->start_time->format('H:i');
            $breakRecord->formatted_break_end_time = $breakRecord->end_time
            ? $breakRecord->end_time->format('H:i')
            : '';

            return $breakRecord;
        });

        return view('staff/detail', compact('userName', 'attendanceRecord', 'correctionRequestStatus'));
    }

}//161624