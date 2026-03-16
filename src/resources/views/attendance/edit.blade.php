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

    <form class="attendance-detail__form" action="{{ route('attendance.update', ['id' => $attendance->id]) }}" method="POST">
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
            <span class="attendance-detail__value attendance-detail__year">
              {{ $attendance->date->format('Y年') }}</span>
            <span class="attendance-detail__separator"></span>
            <span class="attendance-detail__value attendance-detail__date">
              {{ $attendance->date->format('n月j日') }}</span>
          </div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--auto">
          <label class="attendance-detail__label">出勤・退勤</label>
          <div class="attendance-detail__content">
            @if($attendance->correctRequest?->status == 1)
            <span class="attendance-detail__value">
              {{ $attendance->correctRequest->requested_clock_in->format('H:i') }}
            </span>
            <span class="attendance-detail__separator">～</span>
            <span class="attendance-detail__value">
              {{ $attendance->correctRequest->requested_clock_out->format('H:i') ?? '' }}
            </span>
            @else
            <input type="text" name="clock_in" class="attendance-detail__input attendance-detail__value" value="{{ $attendance->clock_in ? $attendance->clock_in->format('H:i') : '' }}">
            <span class="attendance-detail__separator">～</span>
            <input type="text" name="clock_out" class="attendance-detail__input attendance-detail__value" value="{{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '' }}">
            @endif
          </div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--auto">
          <label class="attendance-detail__label">休憩</label>
          <div class="attendance-detail__content">
            @php $rest1 = $attendance->rests->get(0); @endphp
            @if($attendance->correctRequest?->status == 1)
            <span class="attendance-detail__value">
              {{ $rest1 ? $rest1->start_time?->format('H:i') : '' }}
            </span>
            <span class="attendance-detail__separator">～</span>
            <span class="attendance-detail__value">
              {{ $rest1 && $rest1->end_time ? $rest1->end_time?->format('H:i') : '' }}
            </span>
            @else
            <input type="text" name="rest1_start" class="attendance-detail__input attendance-detail__value" value="{{ $rest1 ? $rest1->start_time->format('H:i') : '' }}">
            <span class="attendance-detail__separator">～</span>
            <input type="text" name="rest1_end" class="attendance-detail__input attendance-detail__value" value="{{ $rest1 && $rest1->end_time ? $rest1->end_time->format('H:i') : '' }}">
            @endif
          </div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--auto">
          <label class="attendance-detail__label">休憩2</label>
          <div class="attendance-detail__content">
            @php $rest2 = $attendance->rests->get(1); @endphp
            @if($attendance->correctRequest?->status == 1)
            <span class="attendance-detail__value">
              {{ $rest2 ? $rest2->start_time?->format('H:i') : '' }}
            </span>
            <span class="attendance-detail__separator">～</span>
            <span class="attendance-detail__value">
              {{ $rest2 && $rest2->end_time ? $rest2->end_time?->format('H:i') : '' }}
            </span>
            @else
            <input type="text" name="rest2_start" class="attendance-detail__input attendance-detail__value" value="{{ $rest2 ? $rest2->start_time->format('H:i') : '' }}">
            <span class="attendance-detail__separator">～</span>
            <input type="text" name="rest2_end" class="attendance-detail__input attendance-detail__value" value="{{ $rest2 && $rest2->end_time ? $rest2->end_time->format('H:i') : '' }}">
            @endif
          </div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--description">
          <label class="attendance-detail__label">備考</label>
          <div class="attendance-detail__content">
            @if($attendance->correctRequest?->status == 1)
            <p class="attendance-detail__text">
              {!! nl2br(e($attendance->correctRequest->reason)) !!}
            </p>
            @else
            <textarea name="description" class="attendance-detail__textarea">{{ $attendance->description }}</textarea>
            @endif
          </div>
        </div>
      </div>

      <div class="attendance-detail__actions">
        @if($attendance->correctRequest && $attendance->correctRequest->status == 1)
        <p class="attendance-detail__error-message">
          *承認待ちのため修正はできません。
        </p>
        @else
        <x-black-button type="submit">修正</x-black-button>
        @endif
      </div>
    </form>
  </div>
</div>
@endsection