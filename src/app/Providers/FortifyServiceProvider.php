<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Auth\Events\Login;
use Laravel\Fortify\Http\Requests\RegisterRequest as FortifyRegisterRequest;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;


class FortifyServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    $this->app->singleton(
      \Laravel\Fortify\Contracts\CreatesNewUsers::class,
      \App\Actions\Fortify\CreateNewUser::class
    );
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {

    $this->app->bind(FortifyRegisterRequest::class, RegisterRequest::class);

    $this->app->bind(FortifyLoginRequest::class, LoginRequest::class);

    Fortify::createUsersUsing(CreateNewUser::class);
    Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
    Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
    Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

    RateLimiter::for('login', function (Request $request) {
      $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

      return Limit::perMinute(5)->by($throttleKey);
    });

    RateLimiter::for('two-factor', function (Request $request) {
      return Limit::perMinute(5)->by($request->session()->get('login.id'));
    });

    Fortify::registerView(function () {
      return view('auth.register');
    });

    Fortify::loginView(function () {
      return view('auth.login', ['isAdmin' => false]);
    });

    Fortify::verifyEmailView(function () {
      return view('auth.verify-email');
    });

    $this->app->instance(\Laravel\Fortify\Contracts\LoginResponse::class, new class implements \Laravel\Fortify\Contracts\LoginResponse {
      public function toResponse($request)
      {
        $role = Auth::user()->role;
        $redirect = ($role === 'admin') ? '/admin/attendance/list' : '/attendance';
        return redirect()->intended($redirect);
      }
    });

    $this->app->instance(\Laravel\Fortify\Contracts\VerifyEmailResponse::class, new class implements \Laravel\Fortify\Contracts\VerifyEmailResponse {
      public function toResponse($request)
      {
        return redirect('/attendance')->with('verified', true);
      }
    });
  }
}
