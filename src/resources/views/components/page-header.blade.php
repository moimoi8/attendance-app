<div class="l-main-content">
  <div class="l-content-inner">

    <div class="page-header-common">
      <div class="page-title">
        <h2 class="page-title__text">{{ $title }}</h2>
      </div>
    </div>

    @if($showDateNav ?? false)
    <div class="date-nav">
      <img src="{{ asset('images/arrow.png') }}" alt="←" class="date-nav__arrow--prev">
      <a href="#" class="date-nav__link">前日</a>
      <div class="date-nav__current">
        <div class="date-nav__display">
          <img src="{{ asset('images/calendar-icon.png') }}" alt="" class="date-nav__icon">
          <span class="date-nav_date-text">2023/06/01</span>
        </div>
        <input type="date" class="date-nav__input" value="2023-06-01">
      </div>
      <a href="#" class="date-nav__link">翌日</a>
      <img src="{{ asset('images/arrow.png') }}" alt="→" class="date-nav__arrow--next">
    </div>
    @endif
  </div>
</div>