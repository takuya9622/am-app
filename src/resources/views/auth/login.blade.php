@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/form.css') }}" />
@endsection

@section('content')
<div>
    <div class="form-container">
        <h1>ログイン</h1>
        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf
            <div class="form-group">
                <div class="form-group-label-and-error">
                    <label for="email">メールアドレス</label>
                    @error('email')
                    <p class="error">{{ $message }}</p>
                    @enderror
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <div class="form-group-label-and-error">
                    <label for="password">パスワード</label>
                    @error('password')
                    <p class="error">{{ $message }}</p>
                    @enderror
                </div>
                <input id="password" type="password" name="password" required>
            </div>
            <button type="submit" class="sending-form-button">ログインする</button>
        </form>
    </div>
    <div class="move-register">
        <a href="{{ route('register') }}">会員登録はこちら</a>
    </div>
</div>
@endsection