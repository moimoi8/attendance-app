@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
<link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
@endsection

@section('content')
<div class="l-form-container register-form">
  <h2 class="c-form-heading register-form__heading">会員登録</h2>

  <div class="register-form__inner">
    <form action="{{ route('register') }}" class="register-form__form" method="POST" novalidate>
      @csrf

      <div class="register-form__group">
        <label class="c-form-label register-form__label" for="name">ユーザー名</label>
        <input class="c-form-input register-form__input" type="text" name="name" id="name" value="{{ old('name') }}">
        @error('name')
        <p class="c-form-error">{{ $message }}</p>
        @enderror

      </div>

      <div class="register-form__group">
        <label class="c-form-label register-form__label" for="email">メールアドレス</label>
        <input class="c-form-input register-form__input" type="email" name="email" id="email" value="{{ old('email') }}">

        @error('email')
        <p class="c-form-error">{{ $message }}</p>
        @enderror

      </div>

      <div class="register-form__group">
        <label class="c-form-label register-form__label" for="password">パスワード</label>
        <input class="c-form-input register-form__input" type="password" name="password" id="password">

        @error('password')
        <p class="c-form-error">{{ $message }}</p>
        @enderror
      </div>

      <div class="register-form__group">
        <label class="c-form-label register-form__label" for="password_confirmation">パスワード確認</label>
        <input class="c-form-input register-form__input" type="password" name="password_confirmation" id="password_confirmation">
      </div>

      <div class="register-form__btn-wrap">
        <button class="c-btn-submit register-form__btn-submit" type="submit">登録する</button>
      </div>
    </form>

    <div class="register-form__link">
      <a href="{{ route('login') }}">ログインはこちら</a>
    </div>
  </div>
</div>
@endsection