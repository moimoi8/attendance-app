@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
<link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
<style>
  body {
    background-color: #ffffff;
  }
</style>
@endsection

@section('content')
<div class="l-form-container login-form">
  <h2 class="c-form-heading login-form__heading">
    {{ $isAdmin ? '管理者ログイン' : 'ログイン' }}
  </h2>

  <div class=" login-form__inner">
    <form action="{{ $isAdmin ? route('admin.login') : route('login') }}" class="login-form__form" method="post" novalidate>
      @csrf

      <div class="login-form__group">
        <label class="c-form-label login-form__label" for="email">メールアドレス</label>
        <input class="c-form-input" type="email" name="email" id="email" value="{{ old('email') }}">
        @error('email')
        <p class="c-form-error">{{ $message }}</p>
        @enderror
      </div>

      <div class="login-form__group">
        <label class="c-form-label login-form__label" for="password">パスワード</label>
        <input class="c-form-input" type="password" name="password" id="password" value="{{ old('password') }}">
        @error('password')
        <p class="c-form-error">{{ $message }}</p>
        @enderror
      </div>

      <div class="login-form__btn-wrap">
        <button class="c-btn-submit login-form__btn-submit" type="submit">
          {{ $isAdmin ? '管理者ログイン' : 'ログインする' }}
        </button>
      </div>
    </form>

    @if(!$isAdmin)
    <div class="login-form__link">
      <a href="{{ route('register') }}">会員登録はこちら</a>
    </div>
    @endif
  </div>
</div>
@endsection