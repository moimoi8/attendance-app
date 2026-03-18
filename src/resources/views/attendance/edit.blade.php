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
            <div class="attendance-detail__group">
              <span class="attendance-detail__value attendance-detail__year">
                {{ $attendance->date->format('Y年') }}</span>
              <span class="attendance-detail__separator"></span>
              <span class="attendance-detail__value attendance-detail__date">
                {{ $attendance->date->format('n月j日') }}</span>
            </div>
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
              {{ $attendance->correctRequest->requested_clock_out?->format('H:i') ?? '' }}
            </span>
            @else
            <div class="attendance-detail__input-group">
              <div class="attendance-detail__inputs">
                <input type="text" name="clock_in" class="attendance-detail__input attendance-detail__value" value="{{ old('clock_in', $attendance->clock_in?->format('H:i')) }}">
                <span class="attendance-detail__separator">～</span>
                <input type="text" name="clock_out" class="attendance-detail__input attendance-detail__value" value="{{ old('clock_out',$attendance->clock_out?->format('H:i')) }}">
              </div>
            </div>
            @error('clock_in')
            <p class="attendance-detail__error-message">
              {{ $message }}
            </p>
            @enderror
            @error('clock_out')
            <p class="attendance-detail__error-message">
              {{ $message }}
            </p>
            @enderror
          </div>
          @endif
        </div>

        @foreach($attendance->rests as $index => $rest)
        <div class="attendance-detail__row attendance-detail__row--auto">
          <label class="attendance-detail__label">休憩{{ $index + 1 }}</label>
          <div class="attendance-detail__content">
            @if($attendance->correctRequest?->status == 1)
            <span class="attendance-detail__value">
              {{ $rest->start_time?->format('H:i') }}
            </span>
            <span class="attendance-detail__separator">～</span>
            <span class="attendance-detail__value">
              {{ $rest->end_time?->format('H:i') }}
            </span>
            @else
            <div class="attendance-detail__input-group">
              <div class="attendance-detail__inputs">
                <input type="text" name="rest{{ $index + 1 }}_start" class="attendance-detail__input attendance-detail__value" value="{{ old('rest'.($index+1).'_start', $rest->start_time?->format('H:i') ?? '') }}">
                <span class="attendance-detail__separator">～</span>
                <input type="text" name="rest{{ $index + 1 }}_end" class="attendance-detail__input attendance-detail__value" value="{{ old('rest'.($index+1).'_end', $rest->end_time?->format('H:i') ?? '') }}">
              </div>
              @error('rest'.($index+1).'_start')
              <p class="attendance-detail__error-message">
                {{ $message }}
              </p>
              @enderror
              @error('rest'.($index+1).'_end')
              <p class="attendance-detail__error-message">
                {{ $message }}
              </p>
              @enderror
            </div>
            @endif
          </div>
        </div>
        @endforeach

        @if(!($attendance->correctRequest?->status == 1))
        <div class="attendance-detail__row attendance-detail__row--auto">
          @php $nextIndex = $attendance->rests->count() + 1; @endphp
          <label class="attendance-detail__label">休憩{{ $nextIndex > 1 ? $nextIndex : '' }}</label>
          <div class="attendance-detail__content">
            <div class="attendance-detail__group">
              <input type="text" name="new_rests[0][start]" class="attendance-detail__input attendance-detail__value" value="">
              <span class="attendance-detail__separator">～</span>
              <input type="text" name="new_rests[0][end]" class="attendance-detail__input attendance-detail__value" value="">
            </div>
          </div>
        </div>
        @endif

        <div class="attendance-detail__row attendance-detail__row--description">
          <label class="attendance-detail__label">備考</label>
          <div class="attendance-detail__content">
            @if($attendance->correctRequest?->status == 1)
            <p class="attendance-detail__text">
              {!! nl2br(e($attendance->correctRequest->reason)) !!}
            </p>
            @else
            <div class="attendance-detail__input-group">
              <textarea name="description" class="attendance-detail__textarea">{{ old('description', $attendance->correctRequest?->reason ?? $attendance->description) }}</textarea>
              @error('description')
              <p class="attendance-detail__error-message">
                {{ $message }}
              </p>
              @enderror
            </div>
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