<?php

namespace App\Http\Controllers;

use App\Http\Requests\CorrectionRequest;
use App\Models\AttendanceCorrection;
use App\Models\AttendanceRecord;
use App\Models\BreakCorrection;
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

        $tab = null;
        $staff = null;
        $isApproved = null;

        return view('staff/list', compact('currentMonth', 'attendanceRecords', 'previousMonth', 'nextMonth', 'isApproved'));
    }

    public function edit($attendanceId)
    {
        $isApproved = false;
        $userName = Auth::user()->name;
        $attendanceRecord = AttendanceRecord::with('breakRecords')->find($attendanceId);
        $correctionRequestStatus = $attendanceRecord->correction_request_status;

        if ($correctionRequestStatus === '承認待ち') {
            $attendanceCorrection = AttendanceCorrection::where('attendance_record_id', $attendanceId)
                ->with('attendanceRecord.breakRecords')
                ->latest()
                ->first();
            $attendanceRecord->formatted_year = $attendanceCorrection->correction_date->format('Y');
            $attendanceRecord->formatted_date = $attendanceCorrection-> correction_date->isoFormat('M月D日');
            $attendanceRecord->formatted_clock_in = $attendanceCorrection-> correction_clock_in->format('H:i');
            $attendanceRecord->formatted_clock_out = $attendanceCorrection->correction_clock_out
            ? $attendanceCorrection->correction_clock_out->format('H:i')
            : '';
            $attendanceRecord->remarks = $attendanceCorrection->remarks;

            $attendanceRecord->breakRecords->each(function ($breakRecord) {
                $breakCorrections = BreakCorrection::where('break_record_id', $breakRecord->id)->get();
                $breakRecord->breakCorrections = $breakCorrections;

                $breakCorrections->each(function ($breakCorrection) use ($breakRecord) {
                    $breakRecord->formatted_break_start_time = $breakCorrection->correction_start_time->format('H:i');
                    $breakRecord->formatted_break_end_time = $breakCorrection->correction_end_time
                        ? $breakCorrection->correction_end_time->format('H:i')
                        : '';
                });
            });
        } else {
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
        }
        return view('staff/detail', compact('userName', 'attendanceRecord', 'correctionRequestStatus', 'isApproved'));
    }

    public function correctionRequest(CorrectionRequest $request, $attendanceId)
    {
        $correctionRequestData = $request->validated();
        $attendanceRecord = AttendanceRecord::find($attendanceId);

        $formattedDate = Carbon::createFromFormat('Yn月j日',$correctionRequestData['year'] . $correctionRequestData['date'])->format('Y-m-d');
        $formattedClockIn = $this->combineDateAndTime($formattedDate, $correctionRequestData['clock_in']);
        $formattedClockOut = $this->combineDateAndTime($formattedDate, $correctionRequestData['clock_out']);
        $formattedBreakStartTimes = Arr::get($correctionRequestData, 'break_start_time');
        $formattedBreakEndTimes = Arr::get($correctionRequestData, 'break_end_time');

        $requestedAttendanceRecord = [
            'attendance_record_id' => $attendanceId,
            'correction_date' => $formattedDate,
            'correction_clock_in' => $formattedClockIn,
            'correction_clock_out' => $formattedClockOut,
            'remarks' => $correctionRequestData['remarks'],
        ];

        $requestedBreakRecords = [
            'break_start_time' => $formattedBreakStartTimes,
            'break_end_time' => $formattedBreakEndTimes,
        ];

        $filteredRequestedData = array_filter($requestedAttendanceRecord, function ($value) {
            return !is_null($value);
        });

        $mapping = [
            'date' => 'correction_date',
            'clock_in' => 'correction_clock_in',
            'clock_out' => 'correction_clock_out',
        ];

        $mappedExistingData = [];
        foreach ($mapping as $recordKey => $correctionKey) {
            if (array_key_exists($recordKey, $attendanceRecord->toArray())) {
                $mappedExistingData[$correctionKey] = $attendanceRecord->$recordKey;
            }
        }

        $updatedData = array_keys(array_diff_assoc($filteredRequestedData, $mappedExistingData));
        if (empty($updatedData)) {
            return redirect()->back()->with('message', '変更がありません');
        }

        $attendanceCorrection = AttendanceCorrection::create($filteredRequestedData);

        $breakRecords = BreakRecord::where('attendance_record_id', $attendanceId)->get();
        foreach ($breakRecords as $index => $breakRecord) {
            BreakCorrection::create([
                'break_record_id' => $breakRecord->id,
                'correction_start_time' => $formattedBreakStartTimes[$index] ?? null,
                'correction_end_time' => $formattedBreakEndTimes[$index] ?? null,
            ]);
        }

        $attendanceRecord->update([
            'correction_request_status' => AttendanceRecord::STATUS_PENDING,
        ]);

        return redirect()->route('attendance.detail', $attendanceId)->with('message', '修正リクエストを送信しました');
    }

    private function combineDateAndTime($date, $time)
    {
        return Carbon::createFromFormat('Y-m-d H:i', "$date $time")->format('Y-m-d H:i:s');
    }
}