<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use App\Actions\Fortify\CreateNewUser;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return Redirect::to('/attendance');
                }
            };
        });

        $this->app->singleton(CreatesNewUsers::class, CreateNewUser::class);

        $this->app->bind(RegisteredUserController::class, function ($app) {
            return new class(
                $app->make(StatefulGuard::class),
                $app->make(RegisterResponse::class)
            ) extends RegisteredUserController {

                protected $guard;
                protected $registerResponse;

                public function __construct(StatefulGuard $guard, RegisterResponse $registerResponse)
                {
                    $this->guard = $guard;
                    $this->registerResponse = $registerResponse;
                    parent::__construct($guard, $registerResponse);
                }

                public function store(Request $request, CreatesNewUsers $creator): RegisterResponse
                {
                    $validated = app(RegisterRequest::class)->validated();

                    $user = $creator->create($validated);

                    event(new Registered($user));

                    $this->guard->login($user);

                    return $this->registerResponse;
                }
            };
        });

        $this->app->bind(FortifyLoginRequest::class, LoginRequest::class);

        $this->app->instance(LogoutResponse::class, new class implements LogoutResponse {
            public function toResponse($request)
            {
                return redirect(route('login'));
            }
        });
    }

    public function boot(): void
    {
        Fortify::registerView(function () {
            return view('auth.register');
        });
        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::authenticateUsing(function (LoginRequest $request) {
            $credentials = $request->validated();

            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                if (!$user->hasVerifiedEmail()) {
                    Auth::logout();

                    throw ValidationException::withMessages([
                        Fortify::username() => __('メールアドレスが認証されていません。'),
                    ]);
                }

                return $user;
            }

            $request->failedLogin();
        });
    }
}
