<div class="page-header-common">
  <div class="page-title">
    <h2 class="page-title__text">{{ $title }}</h2>
  </div>
</div>

@if($showDateNav ?? false)
<div class="date-nav">
  <a href="{{ route('admin.attendance.daily', ['date' => \Carbon\Carbon::parse($date)->subDay()->format('Y-m-d')]) }}" class="date-nav__btn date-nav__btn--prev">
    <img src="{{ asset('images/arrow.png') }}" alt="" class="date-nav__arrow">
    <span class="date-nav__btn-text">前日</span>
  </a>
  <div class="date-nav__current">
    <label class="date-nav__label">
      <img src="{{ asset('images/calendar-icon.png') }}" alt="" class="date-nav__icon">
      <span class="date-nav__date-text">{{ \Carbon\Carbon::parse($date)->format('Y/m/d') }}</span>
      <input type="date" class="date-nav__input" value="{{ $date }}" style="display: none;">
    </label>
  </div>
  <a href="{{ route('admin.attendance.daily', ['date' => \Carbon\Carbon::parse($date)->addDay()->format('Y-m-d')]) }}" class="date-nav__btn date-nav__btn--next">
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
        window.location.href = `/admin/attendance/list?date=${selectedDate}`;
      }
    });
  });
</script>