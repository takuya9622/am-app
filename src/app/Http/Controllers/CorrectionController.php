<?php

namespace App\Http\Controllers;

use App\Models\AttendanceCorrection;
use App\Models\AttendanceRecord;
use App\Models\BreakCorrection;
use App\Models\BreakRecord;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class CorrectionController extends Controller
{
    public function list(Request $request)
    {
        $tab = (int) $request->query('tab', AttendanceRecord::STATUS_PENDING);
        $isApproved = false;

        if ($request->is_admin == User::ROLE_ADMIN && session('acting_as_admin')) {
            $attendanceRecordsQuery = AttendanceRecord::with(['user', 'breakRecords.breakCorrections', 'attendanceCorrections'])
            ->whereNotNull('correction_request_status');

            if ($tab === AttendanceRecord::STATUS_PENDING) {
                $attendanceRecordsQuery->pendingCorrection();
            } elseif ($tab === AttendanceCorrection::STATUS_APPROVED) {
                $attendanceRecordsQuery->whereHas('attendanceCorrections', function ($query) {
                    $query->where('status', AttendanceCorrection::STATUS_APPROVED);
                });
                $isApproved = true;
            }
            $attendanceRecords = $attendanceRecordsQuery->get();

            $attendanceRecords->transform(function ($attendanceRecord) {
                $attendanceRecord->staff_name = $attendanceRecord->user->name;
                $attendanceRecord->formatted_date = $attendanceRecord->date->format('Y/m/d');
                $attendanceRecord->formatted_updated_at = $attendanceRecord->updated_at->format('Y/m/d');
                $attendanceRecord->remarks = $attendanceRecord->attendanceCorrections->sortByDesc('created_at')->pluck('remarks')->first();
                $attendanceRecord->correction_id = $attendanceRecord->attendanceCorrections->sortByDesc('created_at')->pluck('id')->first();

                return $attendanceRecord;
            });

            return view('staff/list', compact('tab', 'attendanceRecords', 'isApproved'));
        } else {
            $currentMonth = $request->query('month', now()->format('Y/m'));
            $currentMonthStart = Carbon::createFromFormat('Y/m', $currentMonth)->startOfMonth();
            $currentMonthEnd = Carbon::createFromFormat('Y/m', $currentMonth)->endOfMonth();

            $staff = Auth::user();

            $attendanceRecordsQuery = AttendanceRecord::with(['user', 'breakRecords.breakCorrections', 'attendanceCorrections'])
            ->where('user_id', $staff->id);

            if ($tab === AttendanceRecord::STATUS_PENDING) {
                $attendanceRecordsQuery->pendingCorrection();
            } elseif ($tab === AttendanceCorrection::STATUS_APPROVED) {
                $attendanceRecordsQuery->whereHas('attendanceCorrections', function ($query) {
                    $query->where('status', AttendanceCorrection::STATUS_APPROVED);
                });
                $isApproved = true;
            }

            $attendanceRecords = $attendanceRecordsQuery->get();

            $attendanceRecords->transform(function ($attendanceRecord) use ($staff) {
                $attendanceRecord->staff_name = $staff->name;
                $attendanceRecord->formatted_date = $attendanceRecord->date->format('Y/m/d');
                $attendanceRecord->formatted_updated_at = $attendanceRecord->updated_at->format('Y/m/d');
                $attendanceRecord->remarks = $attendanceRecord->attendanceCorrections->pluck('remarks')->implode(', ');
                $attendanceRecord->correction_id = $attendanceRecord->attendanceCorrections->pluck('id')->first();

                return $attendanceRecord;
            });

            $previousMonth = $currentMonthStart->copy()->subMonth()->format('Y/m');
            $nextMonth = $currentMonthStart->copy()->addMonth()->format('Y/m');

            return view('staff/list', compact('staff', 'tab', 'attendanceRecords', 'isApproved'));
        }
    }

    public function correctionApprove($attendanceId)
    {
        $updatedAttendanceRecord = AttendanceCorrection::with('attendanceRecord.breakRecords.breakCorrections')
        ->where('attendance_record_id', $attendanceId)
        ->latest()
        ->first();

        $updatedBreakRecords = $updatedAttendanceRecord->attendanceRecord->breakRecords
        ->flatMap(function ($breakRecord) {
            return $breakRecord->breakCorrections;
        });

        $attendanceRecord = AttendanceRecord::find($attendanceId);
        $attendanceRecord->update([
            'date' => $updatedAttendanceRecord->correction_date,
            'clock_in' => $updatedAttendanceRecord->correction_clock_in,
            'clock_out' => $updatedAttendanceRecord->correction_clock_out,
            'total_work_minutes' => $attendanceRecord->calculateTotalWorkMinutes(),
            'correction_request_status' => AttendanceRecord::STATUS_APPROVED,
        ]);

        $breakRecords = BreakRecord::where('attendance_record_id', $attendanceId)->get();
        $breakRecords->each(function ($breakRecord) use ($updatedBreakRecords) {
            $updatedBreakRecord = $updatedBreakRecords->where('break_record_id', $breakRecord->id)->last();

            if ($updatedBreakRecord) {
                $breakRecord->update([
                    'start_time' => $updatedBreakRecord->correction_start_time,
                    'end_time' => $updatedBreakRecord->correction_end_time,
                    'break_duration' => $breakRecord->calculateBrakeDuration(),
                ]);
            }
        });

        $attendanceCorrection = AttendanceCorrection::where('attendance_record_id', $attendanceId)
        ->latest('created_at')
        ->first();

        $attendanceCorrection->update([
            'status' => AttendanceCorrection::STATUS_APPROVED,
        ]);

        return redirect()->route('admin.attendance.list', ['tab' => AttendanceRecord::STATUS_PENDING]);
    }

    public function approvedDetail($correctionId)
    {
        $isApproved = true;
        $correctionRequestStatus = null;
        $attendanceRecord = AttendanceCorrection::with('attendanceRecord.breakRecords.breakCorrections', 'attendanceRecord.user')
        ->find($correctionId);
        $userName = $attendanceRecord->attendanceRecord->user->name;

        $attendanceRecord->formatted_year = $attendanceRecord->correction_date->format('Y');
        $attendanceRecord->formatted_date = $attendanceRecord->correction_date->isoFormat('M月D日');
        $attendanceRecord->formatted_clock_in = $attendanceRecord->correction_clock_in->format('H:i');
        $attendanceRecord->formatted_clock_out = $attendanceRecord->correction_clock_out
            ? $attendanceRecord->correction_clock_out->format('H:i')
            : '';
        $attendanceRecord->remarks = $attendanceRecord->remarks;

        $attendanceRecord->attendanceRecord->breakRecords->each(function ($breakRecord) {
            $breakCorrections = BreakCorrection::where('break_record_id', $breakRecord->id)->get();
            $breakRecord->breakCorrections = $breakCorrections;

            $breakCorrections->each(function ($breakCorrection) use ($breakRecord) {
                $breakRecord->formatted_break_start_time = $breakCorrection->correction_start_time->format('H:i');
                $breakRecord->formatted_break_end_time = $breakCorrection->correction_end_time
                    ? $breakCorrection->correction_end_time->format('H:i')
                    : '';
            });
        });

        $attendanceRecord->breakRecords = $attendanceRecord->attendanceRecord->breakRecords;

        return view('staff/detail', compact('userName', 'attendanceRecord', 'correctionRequestStatus', 'isApproved'));
    }
}