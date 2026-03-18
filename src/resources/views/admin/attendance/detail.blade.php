@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">

@endsection

@section('content')
<div class="l-main-content">
  <div class="l-content-inner">

    @include('components.page-header', [
    'title' => '勤怠詳細',
    ])

    <form class="attendance-detail__form" action="{{ route('attendance.update', $attendance->id) }}" method="POST">
      @csrf
      @method('PATCH')
      <div class="attendance-detail__table">
        <div class="attendance-detail__row attendance-detail__row--name">
          <label class="attendance-detail__label">名前</label>
          <div class="attendance-detail__content">
            <span class="attendance-detail__value attendance-detail__user-name">
              {{ $attendance->user->name }}</span>
          </div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--auto">
          <label class="attendance-detail__label">日付</label>
          <div class="attendance-detail__content">
            <span class="attendance-detail__value attendance-detail__year">{{ $attendance->date->format('Y年') }}</span>
            <span class="attendance-detail__separator"></span>
            <span class="attendance-detail__value attendance-detail__date">
              {{ $attendance->date->format('n月j日') }}</span>
          </div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--auto">
          <label class="attendance-detail__label">出勤・退勤</label>
          <div class="attendance-detail__content">
            <input type="text" name="clock_in" class="attendance-detail__input attendance-detail__value" value="{{ $attendance->clock_in ? $attendance->clock_in->format('H:i') : '' }}">
            <span class="attendance-detail__separator">～</span>
            <input type="text" name="clock_out" class="attendance-detail__input attendance-detail__value" value="{{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '' }}">
          </div>
        </div>

        @foreach($attendance->rests as $index => $rest)
        <div class="attendance-detail__row attendance-detail__row--auto">
          <label class="attendance-detail__label">休憩{{ $index > 0 ? $index + 1 : '' }}</label>
          <div class="attendance-detail__content">
            @if($attendance->correctRequest?->status == 1)
            <span class="attendance-detail__value">
              {{ $rest->start_time?->format('H:i') }}
            </span>
            <span class="attendance-detail__separator">～</span>
            <span class="attendance-detail__value">
              {{ $rest->end_time?->format('H:i') ?? '' }}
            </span>
            @else
            <input type="text" name="rests[{{ $rest->id }}][start]" class="attendance-detail__input attendance-detail__value" value="{{ $rest->start_time->format('H:i') }}">
            <span class="attendance-detail__separator">～</span>
            <input type="text" name="rests[{{ $rest->id }}][end]" class="attendance-detail__input attendance-detail__value" value="{{ $rest->end_time ? $rest->end_time->format('H:i') : '' }}">
            @endif
          </div>
        </div>
        @endforeach

        @if(!($attendance->correctRequest?->status == 1))
        <div class="attendance-detail__row attendance-detail__row--auto">
          @php $nextIndex = $attendance->rests->count() + 1; @endphp
          <label class="attendance-detail__label">休憩{{ $nextIndex > 1 ? $nextIndex : '' }}</label>
          <div class="attendance-detail__content">
            <input type="text" name="new_rests[0][start]" class="attendance-detail__input attendance-detail__value" value="">
            <span class="attendance-detail__separator">～</span>
            <input type="text" name="new_rests[0][end]" class="attendance-detail__input attendance-detail__value" value="">
          </div>
        </div>
        @endif

        <div class="attendance-detail__row attendance-detail__row--description">
          <label class="attendance-detail__label">備考</label>
          <div class="attendance-detail__content">
            <textarea class="attendance-detail__textarea" name="description">{{ $attendance->description }}</textarea>
          </div>
        </div>
      </div>

      <div class="attendance-detail__actions">
        <x-black-button type="submit">修正</x-black-button>
      </div>
    </form>
  </div>
</div>
@endsection