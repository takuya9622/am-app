@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_detail.css') }}" />
@endsection

@section('content')
<div class="detail-container">
    <div class="detail-header">
        <h1 class="detail-title">勤怠詳細</h1>
        <div class="message-container">
            @foreach ($errors->all() as $error)
            <p class="error">{{ $error }}</p>
            @endforeach

            @if(session('message'))
            <p class="notice">{{ session('message') }}</p>
            @endif
        </div>
    </div>
    <form
        method="POST"
        class="attendance-correction-form"
        @if(session('acting_as_admin') && $correctionRequestStatus=="承認待ち" )
        action="{{ route('admin.approve', ['attendanceId' => $attendanceRecord->id]) }}"
        @elseif(session('acting_as_admin'))
        action="{{ route('admin.raw.correction', ['attendanceId' => $attendanceRecord->id]) }}"
        @else
        action="{{ route('attendance.correct', ['attendanceId' => $attendanceRecord->id]) }}"
        @endif
        novalidate>
        @csrf
        @if(session('acting_as_admin'))
        @method('PATCH')
        @endif

        <table class="detail-table">
            <tr>
                <th>名前</th>
                <td class="attendance-table-content-name">{{ $userName }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td class="attendance-table-content">
                    <x-selectable-field
                        :isApprovalPending="$correctionRequestStatus ?? null"
                        :isApproved="(bool) $isApproved"
                        id="year"
                        name="year"
                        type="year"
                        :startYear="2000"
                        :endYear="$attendanceRecord->formatted_year"
                        :selected="$attendanceRecord->formatted_year"
                        :value="$attendanceRecord->formatted_year" />
                    <span>&nbsp;</span>
                    <x-selectable-field
                        :isApprovalPending="$correctionRequestStatus ?? null"
                        :isApproved="(bool) $isApproved"
                        id="date"
                        name="date"
                        type="date"
                        :selected="$attendanceRecord->formatted_date"
                        :value="$attendanceRecord->formatted_date" />
                </td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td class="attendance-table-content">
                    <x-editable-field
                        :isApprovalPending="$correctionRequestStatus ?? null"
                        :isApproved="(bool) $isApproved"
                        name="clock_in"
                        :value="$attendanceRecord->formatted_clock_in" />
                    <span>～</span>
                    <x-editable-field
                        :isApprovalPending="$correctionRequestStatus ?? null"
                        :isApproved="(bool) $isApproved"
                        name="clock_out"
                        :value="$attendanceRecord->formatted_clock_out"
                        class="table-row-end" />
                </td>
            </tr>
            @foreach($attendanceRecord->breakRecords as $breakRecord)
            <tr>
                <th>休憩{{ $loop->iteration > 1 ? $loop->iteration : '' }}</th>
                <td class="attendance-table-content">
                    <x-editable-field
                        :isApprovalPending="$correctionRequestStatus ?? null"
                        :isApproved="(bool) $isApproved"
                        name="break_start_time[{{ $loop->index }}]"
                        :value="$breakRecord->formatted_break_start_time" />
                    <span>～</span>
                    <x-editable-field
                        :isApprovalPending="$correctionRequestStatus ?? null"
                        :isApproved="(bool) $isApproved"
                        name="break_end_time[{{ $loop->index }}]"
                        :value="$breakRecord->formatted_break_end_time"
                        class="table-row-end" />
                </td>
            </tr>
            @endforeach
            <tr class="detail-remarks">
                <th>備考</th>
                <td class="attendance-table-content" colspan="3">
                    @if($correctionRequestStatus === '承認待ち' || $isApproved === true)
                    <p class="form-content-remarks">{{ $attendanceRecord->remarks }}</p>
                    @else
                    <textarea name="remarks" rows="3">{{ old('remarks') ?? $attendanceRecord->remarks }}</textarea>
                    @endif
                </td>
            </tr>
        </table>
        @if($isApproved === true)
        <label class="approved-label">承認済み</label>
        @elseif(session('acting_as_admin') && $correctionRequestStatus !== '承認待ち')
        <button type="submit" class="correction-button">修正</button>
        @elseif(session('acting_as_admin'))
        <button type="submit" class="correction-button">承認</button>
        @elseif($correctionRequestStatus === '承認待ち')
        <p class="correction-request-pending">*承認待ちのため修正はできません。</p>
        @else
        <button type="submit" class="correction-button">修正</button>
        @endif
    </form>
</div>
@endsection