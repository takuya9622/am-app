@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
@endsection

@section('content')
<div class="list-container">
    <div class="list-header">
        <h1 class="list-title">勤怠一覧</h1>
        <div class="list-nav">
            <div class="list-nav-month">
                <span class="material-icons">
                    west
                </span>
                <a href="{{ route('attendance.list', ['month' => $previousMonth]) }}" class="list-nav-btn">前月</a>
            </div>
            <p class="list-nav-date">
                <span class="material-icons">
                    calendar_month
                </span>
                {{ $currentMonth }}
            </p>
            <div class="list-nav-month">
                <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}" class="list-nav-btn">翌月</a>
                <span class="material-icons">
                    east
                </span>
            </div>
        </div>
    </div>

    <div class="attendance-table-container">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendanceRecords as $attendanceRecord)
                <tr class="attendance-table-row">
                    <td>{{ $attendanceRecord->formatted_date }}</td>
                    <td>{{ $attendanceRecord->formatted_clock_in }}</td>
                    <td>{{ $attendanceRecord->formatted_clock_out }}</td>
                    <td>{{ $attendanceRecord->formatted_break_time }}</td>
                    <td>{{ $attendanceRecord->formatted_work_time }}</td>
                    <td class="attendance-detail"><a href="{{ route('attendance.detail', ['attendanceId' => $attendanceRecord->id]) }}">詳細</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection