@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}" />
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
@endsection

@section('content')
<div class="list-container">
    <div class="list-header">
        <h1 class="list-title">スタッフ一覧</h1>
    </div>

    <div class="attendance-table-container">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>月次勤怠</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($staff as $employee)
                <tr class="attendance-table-row">
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->email }}</td>
                    <td class="attendance-detail"><a href="{{ route('admin.attendance.staff', ['staffId' => $employee->id]) }}">詳細</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection