<div>
    <div class="form-container">
        <h1>{{ $title }}</h1>
        <form method="POST" action="{{ route($action) }}" novalidate>
            @csrf
            @if ($action === 'register')
            <div class="form-group">
                <div class="form-group-label-and-error">
                    <label for="name">名前</label>
                    @error('name')
                    <p class="error">{{ $message }}</p>
                    @enderror
                </div>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
            </div>
            @endif
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
            @if ($action === 'register')
            <div class="form-group">
                <div class="form-group-label-and-error">
                    <label for="password_confirmation">パスワード確認</label>
                    @error('password_confirmation')
                    <p class="error">{{ $message }}</p>
                    @enderror
                </div>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>
            @endif
            <button type="submit" class="sending-form-button">{{ $buttonText }}</button>
        </form>
    </div>

    <div class="move-register">
        @if ($action === 'register')
        <a href="{{ route('login') }}">ログインはこちら</a>
        @elseif ($action === 'login')
        <a href="{{ route('register') }}">会員登録はこちら</a>
        @endif
    </div>
</div>