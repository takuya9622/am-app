@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
@endsection

@section('content')
<div class="list-container">
    <x-list-header
        :tab="$tab ?? null"
        :staff="$staff ?? null"
        :previousMonth="$previousMonth ?? null"
        :nextMonth="$nextMonth ?? null"
        :currentMonth="$currentMonth ?? null"
        :todayFormatted="$todayFormatted ?? null"
        :yesterday="$yesterday ?? null"
        :tomorrow="$tomorrow ?? null" />

    <x-attendance-table
        :attendanceRecords="$attendanceRecords"
        :isApproved="(bool) $isApproved ?? false" />
</div>
@endsection