<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\BreakRecord;
use App\Models\User;
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

        return view('staff/attendance', compact('now', 'user', 'attendanceRecord'));
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
}