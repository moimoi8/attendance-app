<div class="page-header-common">
  <div class="page-title">
    <h2 class="page-title__text">{{ $title }}</h2>
  </div>
</div>

@if($showDateNav ?? false)
<div class="date-nav">
  <a href="#" class="date-nav__btn date-nav__btn--prev">
    <img src="{{ asset('images/arrow.png') }}" alt="" class="date-nav__arrow">
    <span class="date-nav__btn-text">前日</span>
  </a>
  <div class="date-nav__current">
    <div class="date-nav__display">
      <img src="{{ asset('images/calendar-icon.png') }}" alt="" class="date-nav__icon">
      <span class="date-nav_date-text">{{ \Carbon\Carbon::parse($date)->format('y/m/d') }}</span>
    </div>
    <input type="date" class="date-nav__input" value="2023-06-01">
  </div>
  <a href="#" class="date-nav__btn date-nav__btn--next">
    <span class="date-nav__btn-text">翌日</span>
    <img src="{{ asset('images/arrow.png') }}" alt="" class="date-nav__arrow">
  </a>
</div>
@endif

document.addEventListener('DOMContentLoaded', () => {
const dateInput = document.querySelector('.date-nav__input');

dateInput.addEventListener('change', (e) => {
const selectedDate = e.target.value;

if (selectedDate) {
window.location.href = `/attendance/${selectedDate}`;
}
});
});