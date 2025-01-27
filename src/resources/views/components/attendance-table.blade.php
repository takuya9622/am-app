<div class="attendance-table-container">
    <table class="attendance-table">
        <thead>
            <tr>
                @if(Route::is('correction.request.list'))
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日次</th>
                @else
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                @endif
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @if(Route::is('correction.request.list'))
            @foreach ($attendanceRecords as $attendanceRecord)
            <tr class="attendance-table-row">
                <td>{{ $attendanceRecord->correction_request_status }}</td>
                <td>{{ $attendanceRecord->staff_name }}</td>
                <td>{{ $attendanceRecord->formatted_date }}</td>
                <td>{{ $attendanceRecord->remarks }}</td>
                <td>{{ $attendanceRecord->formatted_updated_at }}</td>
                <td class="attendance-detail"><a href="{{ route('attendance.detail', ['attendanceId' => $attendanceRecord->id]) }}">詳細</a></td>
            </tr>
            @endforeach

            @else

            @foreach ($attendanceRecords as $attendanceRecord)
            <tr class="attendance-table-row">
                <td>{{ $attendanceRecord->formatted_date ?? $attendanceRecord->staff_name }}</td>
                <td>{{ $attendanceRecord->formatted_clock_in }}</td>
                <td>{{ $attendanceRecord->formatted_clock_out }}</td>
                <td>{{ $attendanceRecord->formatted_break_time }}</td>
                <td>{{ $attendanceRecord->formatted_work_time }}</td>
                <td class="attendance-detail"><a href="{{ route('attendance.detail', ['attendanceId' => $attendanceRecord->id]) }}">詳細</a></td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
</div>