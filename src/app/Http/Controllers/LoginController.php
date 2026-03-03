<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
