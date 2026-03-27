@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="verify-email">
  <div class="verify-email__inner">

    <div class="verify-email__content">
      <p class="verify-email__text">
        登録していただいたメールアドレスに認証メールを送信しました。<br>
        メール認証を完了してください。
      </p>
    </div>

    <div class="verify-email__btn-wrap">
      <a href="http://localhost:8025" class="verify-email__btn-confirm" target="_blank">認証はこちらから</a>
    </div>

    <div class="verify-email__resend">
      <form class="d-inline" action="{{ route('verification.send') }}" method="POST">
        @csrf
        <button type="submit" class="verify-email__resend-link">
          認証メールを再送する
        </button>
      </form>
    </div>
  </div>
</div>
@endsection