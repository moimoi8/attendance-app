<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>COACHTECH</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  <link rel="stylesheet" href="{{ asset('css/header.css') }}">
  <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
  @yield('css')
</head>

<body>

  <header class="header">
    <div class="header__inner">
      <div class="header__logo">
        <a href="/" class="header__logo-link">
          <img src="{{ asset('img/header-logo.png') }}" alt="COACHTECH" class="header__logo-img">
        </a>
      </div>
      @if( !in_array(Route::currentRouteName(), ['register', 'login']) )
      @auth

      <nav class="header__nav">
        <ul class="header__nav-list">
          @if(Auth::user()->isAdmin())
          <li class="header__nav-item">
            <a href="/admin/attendance" class="header__nav-link">勤怠一覧</a>
          </li>
          <li class="header__nav-item">
            <a href="/admin/staff" class="header__nav-link">スタッフ一覧</a>
          </li>
          <li class="header__nav-item">
            <a href="/admin/requests" class="header__nav-link">申請一覧</a>
          </li>
          @else
          @if(Route::currentRouteName() == 'attendance.clockout')
          <li class="header__nav-item">
            <a href="/attendance/monthly" class="header__nav-link">今月の出勤一覧</a>
          </li>
          <li class="header__nav-item">
            <a href="/requests" class="header__nav-link">申請一覧</a>
          </li>
          @else
          <li class="header__nav-item">
            <a href="/attendance" class="header__nav-link">勤怠</a>
          </li>
          <li class="header__nav-item">
            <a href="/attendance/list" class="header__nav-link">勤怠一覧</a>
          </li>
          <li class="header__nav-item">
            <a href="/requests" class="header__nav-link">申請</a>
          </li>
          @endif
          @endif

          <li class="header__nav-item">
            <form action="/logout" method="post">
              @csrf
              <button class="logout__link" type="submit">ログアウト</button>
            </form>
          </li>
        </ul>
      </nav>
      @endauth
      @endif
    </div>
  </header>

  <main>
    @yield('content')
  </main>

</body>

</html>