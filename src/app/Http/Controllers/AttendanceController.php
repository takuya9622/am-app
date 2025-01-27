<?php

namespace App\Http\Controllers;

use App\Http\Requests\CorrectionRequest;
use App\Models\AttendanceRecord;
use App\Models\BreakRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class AttendanceController extends Controller
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

        $attendanceRecord->formatted_year = $attendanceRecord->date->format('Y');
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

    public function correct(CorrectionRequest $request, $attendanceId)
    {
        //dd(array_map('gettype', $request->all()));
        $correctionRequestData = $request->validated();
        $attendanceRecord = AttendanceRecord::with('breakRecords')->find($attendanceId);

        $formattedDate = Carbon::createFromFormat('Yn月j日',$correctionRequestData['year'] . $correctionRequestData['date'])->format('Y-m-d');
        $formattedClockIn = $this->combineDateAndTime($formattedDate, $correctionRequestData['clock_in']);
        $formattedClockOut = $this->combineDateAndTime($formattedDate, $correctionRequestData['clock_out']);

        $requestedAttendanceRecord = new AttendanceRecord([
            'date' => $formattedDate,
            'clock_in' => $formattedClockIn,
            'clock_out' => $formattedClockOut,
            'remarks' => $correctionRequestData['remarks'],
        ]);
        $requestedAttendanceRecord->total_work_minutes = $requestedAttendanceRecord->calculateTotalWorkMinutes();

        $updatedData = array_keys(array_diff_assoc($requestedAttendanceRecord->toArray(), $attendanceRecord->toArray()));
        if (empty($updatedData)) {
            return redirect()->back()->with('message', '変更がありません');
        }

        $attendanceRecord->update([
            'date' => $requestedAttendanceRecord->date,
            'clock_in' => $requestedAttendanceRecord->clock_in,
            'clock_out' => $requestedAttendanceRecord->clock_out,
            'remarks' => $requestedAttendanceRecord->remarks,
            'total_work_minutes' => $requestedAttendanceRecord->total_work_minutes,
            'correction_request_status' => AttendanceRecord::STATUS_PENDING,
        ]);

        return redirect()->route('attendance.detail', $attendanceId)->with('message', '修正リクエストを送信しました');
    }

    private function combineDateAndTime($date, $time)
    {
        return Carbon::createFromFormat('Y-m-d H:i', "$date $time")->format('Y-m-d H:i:s');
    }
}