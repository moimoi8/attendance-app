@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/punch.css') }}">
@endsection

@section('content')
<div class="punch">
  <div class="punch__inner">
    <div class="punch__status punch__status--{{ $status_key }}">
      {{ $status_label }}
    </div>

    <div class="punch__date">{{ $today }}</div>
    <div class="punch__time">{{ $current_time }}</div>

    @if($status == 'finished')
    <p class="punch__message">お疲れ様でした。</p>
    @else
    <div class="punch__button-container">
      @if($status == 'off_duty')
      <button class="punch__button punch__button--black">出勤</button>
      @elseif($status == 'working')
      <button class="punch__button punch__button--white">休憩入</button>
      @elseif($status == 'on_break')
      <button class="punch__button punch__button--white">休憩戻</button>
      @endif
    </div>
    @endif
  </div>
</div>

<script>
  function updateDateTime() {
    const now = new Date();

    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    document.querySelector('.punch__time').textContent = `${hours}:${minutes}`;

    const year = now.getFullYear();
    const month = now.getMonth() + 1;
    const date = now.getDate();
    const dayList = ["日", "月", "火", "水", "木", "金", "土"];
    const day = dayList[now.getDay()];

    const dateString = `${year}年${month}月${date}日(${day})`;
    document.querySelector('.punch__date').textContent = dateString;
  }

  setInterval(updateDateTime, 1000);
  updateDateTime();
</script>