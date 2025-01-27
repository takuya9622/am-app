<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
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

        if ($request->is_admin) {
            $todayFormatted = $request->query('date', now()->format('Y/m/d'));
            $today = Carbon::createFromFormat('Y/m/d', $todayFormatted)->format('Y-m-d');

            $attendanceRecordsQuery = AttendanceRecord::with(['user', 'breakRecords'])
            ->whereNotNull('correction_request_status');

            if ($tab === AttendanceRecord::STATUS_PENDING) {
                $attendanceRecordsQuery->pendingCorrection();
            } elseif ($tab === AttendanceRecord::STATUS_APPROVED) {
                $attendanceRecordsQuery->approvedCorrection();
            }

            $attendanceRecords = $attendanceRecordsQuery->get();

            $attendanceRecords->transform(function ($attendanceRecord) {
                $attendanceRecord->staff_name = $attendanceRecord->user->name;
                $attendanceRecord->formatted_date = $attendanceRecord->date->format('Y/m/d');
                $attendanceRecord->formatted_updated_at = $attendanceRecord->updated_at->format('Y/m/d');

                return $attendanceRecord;
            });

            $tab = null;

            return view('staff/list', compact('tab', 'attendanceRecords'));
        }

        $tab = (int) $request->query('tab', AttendanceRecord::STATUS_PENDING);

        $currentMonth = $request->query('month', now()->format('Y/m'));
        $currentMonthStart = Carbon::createFromFormat('Y/m', $currentMonth)->startOfMonth();
        $currentMonthEnd = Carbon::createFromFormat('Y/m', $currentMonth)->endOfMonth();

        $staff = Auth::user();

        $attendanceRecordsQuery = AttendanceRecord::with(['user', 'breakRecords'])
            ->where('user_id', $staff->id);

        if ($tab === AttendanceRecord::STATUS_PENDING) {
            $attendanceRecordsQuery->pendingCorrection();
        } elseif ($tab === AttendanceRecord::STATUS_APPROVED) {
            $attendanceRecordsQuery->approvedCorrection();
        }

        $attendanceRecords = $attendanceRecordsQuery->get();

        $attendanceRecords->transform(function ($attendanceRecord) use ($staff) {
            $attendanceRecord->staff_name = $staff->name;
            $attendanceRecord->formatted_date = $attendanceRecord->date->format('Y/m/d');
            $attendanceRecord->formatted_updated_at = $attendanceRecord->updated_at->format('Y/m/d');

            return $attendanceRecord;
        });

        $previousMonth = $currentMonthStart->copy()->subMonth()->format('Y/m');
        $nextMonth = $currentMonthStart->copy()->addMonth()->format('Y/m');

        return view('staff/list', compact('staff', 'tab', 'attendanceRecords'));
    }
}
