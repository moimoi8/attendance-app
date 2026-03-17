<div class="page-header-common">
  <div class="page-title">
    <h2 class="page-title__text">{{ $title }}</h2>
  </div>
</div>

@if($showDateNav ?? false)
<div class="date-nav">
  @php
  $carbonDate = \Carbon\Carbon::parse($date);
  $routeName = (Auth::user()->role === 'admin') ? 'admin.attendance.daily' : 'attendance.list';

  if (Auth::user()->role === 'admin') {
  $prev = $carbonDate->copy()->subDay()->format('Y-m-d');
  $next = $carbonDate->copy()->addDay()->format('Y-m-d');
  $displayDate = $carbonDate->format('Y-m-d');
  } else {
  $prev = $carbonDate->copy()->subMonth()->format('Y-m-d');
  $next = $carbonDate->copy()->addMonth()->format('Y-m-d');
  $displayDate = $carbonDate->format('Y/m');
  }
  @endphp

  <a href="{{ route($routeName, ['date' => $prev]) }}" class="date-nav__btn date-nav__btn--prev">
    <img src="{{ asset('images/arrow.png') }}" alt="" class="date-nav__arrow">
    <span class="date-nav__btn-text">前日</span>
  </a>
  <div class="date-nav__current">
    <label class="date-nav__label">
      <img src="{{ asset('images/calendar-icon.png') }}" alt="" class="date-nav__icon">
      <span class="date-nav__date-text">{{ \Carbon\Carbon::parse($date)->format('Y/m/d') }}</span>
      <input type="date" class="date-nav__input" value="{{ $date }}" data-is-admin="{{ Auth::user()->role === 'admin' ? 'true' : 'false' }}" style="display: none;">
    </label>
  </div>
  <a href="{{ route($routeName, ['date' => $next]) }}" class="date-nav__btn date-nav__btn--next">
    <span class="date-nav__btn-text">翌日</span>
    <img src="{{ asset('images/arrow.png') }}" alt="" class="date-nav__arrow">
  </a>
</div>
@endif
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const dateInput = document.querySelector('.date-nav__input');
    const label = document.querySelector('.date-nav__label');

    label.addEventListener('click', (e) => {
      if (dateInput.showPicker) {
        dateInput.showPicker();
      }
    });

    dateInput.addEventListener('change', (e) => {
      const selectedDate = e.target.value;
      if (selectedDate) {
        const isAdmin = dateInput.dataset.isAdmin === 'true';
        const baseUrl = isAdmin ? '/admin/attendance/list' : '/attendance/list';
        window.location.href = `${baseUrl}?date=${selectedDate}`;
      }
    });
  });
</script>