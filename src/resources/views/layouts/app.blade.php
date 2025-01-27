<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>COACHTECH 勤怠管理</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
    @yield('css')
</head>

@php
$currentRoute = Route::currentRouteName();
$isAuthPage = in_array($currentRoute, ['login', 'register', 'admin.login']);
@endphp

<body class="{{ $isAuthPage ? 'auth-page' : 'function-page' }}">
    <header class="header">
        <div class="header-container">
            <div class="header-logo">
                <a href="{{  route('attendance.index') }}">
                    <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH">
                </a>
            </div>

            @auth
            <div class="header-nav">
                <ul class="menu-items">
                    @admin
                    <li><a href="{{ route('admin.attendance.list') }}">勤怠一覧</a></li>
                    <li><a href="{{ route('admin.staff.index') }}">スタッフ一覧</a></li>
<<<<<<< HEAD
                    <li><a href="{{ route('correction.request.list') }}">申請一覧</a></li>
=======
<<<<<<< Updated upstream
                    <li><a href="{{ route('correction.request.list') }}">申請一覧</a></li>
=======
<<<<<<< Updated upstream
                    <li><a href="">申請一覧</a></li>
=======
                    <li><a href="{{ route('correction.request.list') }}">申請一覧</a></li>
>>>>>>> Stashed changes
>>>>>>> Stashed changes
>>>>>>> function
                    @else
                    <li><a href="{{  route('attendance.index') }}">勤怠</a></li>
                    <li><a href="{{  route('attendance.list') }}">勤怠一覧</a></li>
                    <li><a href="{{ route('correction.request.list') }}">申請</a></li>
                    @endadmin
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="logout-form">
                            @csrf
                            <button type="submit" class="header-nav-link">ログアウト</button>
                        </form>
                    </li>
                </ul>
            </div>
            @endauth
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>