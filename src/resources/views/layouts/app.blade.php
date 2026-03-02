<header class="header">
  <div class="header__logo">
    <a href="/"><img src="{{ asset('img/header-logo.png') }}" alt="ロゴ"></a>
  </div>
  @if( !in_array(Route::currentRouteName(), ['register', 'login']) )

  <nav class="header__nav">
    <ul>
      @auth
      @if(Auth::user()->isAdmin())
      <li><a href="/admin/attendance">勤怠一覧</a></li>
      <li><a href="/admin/staff">スタッフ一覧</a></li>
      <li><a href="/admin/requests">申請一覧</a></li>
      @else
      @if(Route::currentRouteName() == 'attendance.clockout')
      <li><a href="/attendance/monthly">今月の出勤一覧</a></li>
      <li><a href="/requests">申請一覧</a></li>
      @else
      <li><a href="/attendance">勤怠</a></li>
      <li><a href="/attendance/list">勤怠一覧</a></li>
      <li><a href="/requests">申請</a></li>
      @endif
      @endif

      <li>
        <form action="/logout" method="post">
          @csrf
          <button class="logout__link" type="submit">ログアウト</button>
        </form>
      </li>
      @endauth
    </ul>
  </nav>
  @endif
</header>