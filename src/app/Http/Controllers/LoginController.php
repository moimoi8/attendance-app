<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
  public function showUserLoginForm()
  {
    return view('auth.login', ['isAdmin' => false]);
  }

  public function showAdminLoginForm()
  {
    return view('auth.login', ['isAdmin' => true]);
  }

  public function store(LoginRequest $request)
  {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
      $request->session()->regenerate();

      $user = Auth::user();

      if ($user->role === 'admin') {
        return redirect()->intended('/admin/attendance/list');
      }
      return redirect()->intended('/attendance');
    }
    return back()->withErrors([
      'email' => 'ログイン情報が登録されていません',
    ])->onlyInput('email');
  }
}
