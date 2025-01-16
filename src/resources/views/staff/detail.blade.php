@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_detail.css') }}" />
@endsection

@section('content')
<div class="detail-container">
    <div class="detail-header">
        <h1 class="detail-title">勤怠詳細</h1>
        <div class="error-container">
            @foreach ($errors->all() as $error)
            <p class="error">{{ $error }}</p>
            @endforeach
        </div>
    </div>
    <form method="POST" class="attendance-correction-form" action="{{ route('attendance.correct', ['attendanceId' => $attendanceRecord->id]) }}" novalidate>
        @csrf
        @method('PATCH')
        <table class="detail-table">
            <tr>
                <th>名前</th>
                <td class="attendance-table-content-name">{{ $userName }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td class="attendance-table-content">
                    <x-editable-field :isApprovalPending="$correctionRequestStatus" type="text" name="year" :value="$attendanceRecord->formatted_year" />
                    <span>&nbsp;</span>
                    <x-editable-field :isApprovalPending="$correctionRequestStatus" type="text" name="date" :value="$attendanceRecord->formatted_date" class="table-row-end" />
                </td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td class="attendance-table-content">
                    <x-editable-field :isApprovalPending="$correctionRequestStatus" name="clock_in" :value="$attendanceRecord->formatted_clock_in" />
                    <span>～</span>
                    <x-editable-field :isApprovalPending="$correctionRequestStatus" name="clock_out" :value="$attendanceRecord->formatted_clock_out" class="table-row-end" />
                </td>
            </tr>
            @foreach($attendanceRecord->breakRecords as $breakRecord)
            <tr>
                <th>休憩{{ $loop->iteration > 1 ? $loop->iteration : '' }}</th>
                <td class="attendance-table-content">
                    <x-editable-field :isApprovalPending="$correctionRequestStatus" name="break_start_time[{{ $loop->index }}]" :value="$breakRecord->formatted_break_start_time" />
                    <span>～</span>
                    <x-editable-field :isApprovalPending="$correctionRequestStatus" name="break_end_time[{{ $loop->index }}]" :value="$breakRecord->formatted_break_end_time" class="table-row-end" />
                </td>
            </tr>
            @endforeach
            <tr class="detail-remarks">
                <th>備考</th>
                <td class="attendance-table-content" colspan="3">
                    @if($correctionRequestStatus === '承認待ち')
                    <p class="form-content-remarks">{{ $attendanceRecord->remarks }}</p>
                    @else
                    <textarea name="remarks" rows="3">{{ old('remarks') ?? $attendanceRecord->remarks }}</textarea>
                    @endif
                </td>
            </tr>
        </table>
        @if($correctionRequestStatus === '承認待ち')
        <p class="correction-request-pending">*承認待ちのため修正はできません。</p>
        @else
        <button type="submit" class="correction-button">修正</button>
        @endif
    </form>
</div>

@endsection