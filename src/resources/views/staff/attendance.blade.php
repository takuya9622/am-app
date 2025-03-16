@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_input.css') }}" />
@endsection

@section('content')
<div class="attendance-container">
    @php
    $status = $attendanceRecord->work_status ?? null;
    $statusText = $status ? $status : '勤務外';
    @endphp

    <div class="status">{{ $statusText }}</div>

    <div class="date">{{ $now->isoFormat('YYYY年M月D日(ddd)') }}</div>

    <div class="time">{{ $now->format('H:i') }}</div>

    <div class="button-container">
        @if($attendanceRecord === null)
        <form method="POST" action="{{ route('attendance.store') }}">
            @csrf
            <button type="submit" class="button work">出勤</button>
        </form>

        @elseif($attendanceRecord->work_status == '出勤中')
        <form method="POST" action="{{ route('attendance.update', $attendanceRecord) }}">
            @method('PUT')
            @csrf
            <input type="hidden" name="action" value="endWork">
            <button type="submit" class="button work">退勤</button>
        </form>
        <form method="POST" action="{{ route('attendance.update', $attendanceRecord) }}">
            @method('PUT')
            @csrf
            <input type="hidden" name="action" value="startBreak">
            <button class="button break">休憩入</button>
        </form>

        @elseif($attendanceRecord->work_status == '休憩中')
        <form method="POST" action="{{ route('attendance.update', $attendanceRecord) }}">
            @method('PUT')
            @csrf
            <input type="hidden" name="action" value="endBreak">
            <button class="button break">休憩戻</button>
        </form>

        @else($attendanceRecord->work_status == '退勤済')
        <p class="message">お疲れさまでした。</p>

        @endif
    </div>
</div>
@endsection