<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AuthForm extends Component
{
    public $action;
    public $title;
    public $buttonText;

    public function __construct($action = 'register')
    {
        $this->action = $action;

        $this->title = match ($action) {
            'register' => '会員登録',
            'login' => 'ログイン',
            'admin.login' => '管理者ログイン',
            default => '不明なアクション',
        };

        $this->buttonText = match ($action) {
            'register' => '登録する',
            'login' => 'ログインする',
            'admin.login' => '管理者ログインする',
            default => '送信',
        };

    }

    public function render(): View|Closure|string
    {
        return view('components.auth-form');
    }
}
