<div class="list-header">
    @if(Route::is('*.correction.list'))
    <h1 class="list-title">申請一覧</h1>
    @elseif(Route::is('admin.attendance.staff'))
    <h1 class="list-title">{{ $staff->name }}さんの勤怠</h1>
    @elseif(Route::is('admin.attendance.list'))
    <h1 class="list-title">{{ $todayFormatted }}の勤怠</h1>
    @else
    <h1 class="list-title">勤怠一覧</h1>
    @endif

    @if(Route::is('*.correction.list'))
    <nav class="items-tab">
        <ul class="tabs">
            <li class="{{ (int)$tab === \App\Models\AttendanceRecord::STATUS_PENDING ? 'active-tab' : '' }}">
                <a href="{{ route('correction.request.list',
                ['tab' => \App\Models\AttendanceRecord::STATUS_PENDING]) }}">
                    承認待ち
                </a>
            </li>
            <li class="{{ (int)$tab === \App\Models\AttendanceCorrection::STATUS_APPROVED ? 'active-tab' : '' }}">
                <a href="{{ route('correction.request.list',
                ['tab' => \App\Models\AttendanceCorrection::STATUS_APPROVED]) }}">
                    承認済み
                </a>
            </li>
        </ul>
    </nav>

    @else

    <div class="list-nav">
        <div class="list-nav-month">
            <span class="material-icons">
                west
            </span>
            @if(Route::is('admin.attendance.list'))
            <a href="{{ route('admin.attendance.list', ['date' => $yesterday]) }}" class="list-nav-btn">前日</a>
            @elseif(Route::is('admin.attendance.staff'))
            <a href="{{ route('admin.attendance.staff', ['staffId' => $staff->id, 'month' => $previousMonth]) }}" class="list-nav-btn">前月</a>
            @else
            <a href="{{ route('attendance.list', ['month' => $previousMonth]) }}" class="list-nav-btn">前月</a>
            @endif
        </div>
        <p class="list-nav-date">
            <span class="material-icons">
                calendar_month
            </span>
            {{ $currentMonth ?? $todayFormatted }}
        </p>
        <div class=" list-nav-month">
            @if(Route::is('admin.attendance.list'))
            <a href="{{ route('admin.attendance.list', ['date' => $tomorrow]) }}" class="list-nav-btn">翌日</a>
            @elseif(Route::is('admin.attendance.staff'))
            <a href="{{ route('admin.attendance.staff', ['staffId' => $staff->id, 'month' => $nextMonth]) }}" class="list-nav-btn">翌月</a>
            @else
            <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}" class="list-nav-btn">翌月</a>
            @endif
            <span class="material-icons">
                east
            </span>
        </div>
    </div>
    @endif
</div>