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

    <div class="attendance-detail__form">
      <div class="attendance-detail__table">
        <div class="attendance-detail__row attendance-detail__row--name">
          <label class="attendance-detail__label">名前</label>
          <div class="attendance-detail__content">
            <span class="attendance-detail__value attendance-detail__user-name">
              {{ $application->attendance->user->name }}</span>
          </div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--auto">
          <label class="attendance-detail__label">日付</label>
          <div class="attendance-detail__content">
            <div class="attendance-detail__group attendance-detail__date-wrapper">
              <span class="attendance-detail__value attendance-detail__year">
                {{ $application->attendance->date->format('Y年') }}</span>
              <span class="attendance-detail__value attendance-detail__date">
                {{ $application->attendance->date->format('n月j日') }}</span>
            </div>
          </div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--auto">
          <label class="attendance-detail__label">出勤・退勤</label>
          <div class="attendance-detail__content attendance-detail__content--view">
            <span class="attendance-detail__value attendance-detail__value--text">
              {{ optional($application->requested_clock_in)->format('H:i') }}
            </span>
            <span class="attendance-detail__separator">～</span>
            <span class="attendance-detail__value attendance-detail__value--text">
              {{ optional($application->requested_clock_out)->format('H:i') }}
            </span>
          </div>
        </div>

        @foreach($attendance->rests as $index => $rest)
        <div class="attendance-detail__row attendance-detail__row--auto">
          <label class="attendance-detail__label">休憩{{ $index + 1 }}</label>
          <div class="attendance-detail__content attendance-detail__content--view">
            <span class="attendance-detail__value attendance-detail__value--text">
              {{ $rest->start_time?->format('H:i') }}
            </span>
            <span class="attendance-detail__separator">～</span>
            <span class="attendance-detail__value attendance-detail__value--text">
              {{ $rest->end_time?->format('H:i') }}
            </span>
          </div>
        </div>
        @endforeach

        <div class="attendance-detail__row attendance-detail__row--description">
          <label class="attendance-detail__label">備考</label>
          <div class="attendance-detail__content">
            <p class="attendance-detail__text">
              {!! nl2br(e($application->reason)) !!}
            </p>
          </div>
        </div>
      </div>

      <div class="attendance-detail__actions">
        @if($application->status == 1)
        <form action="{{ route('admin.approve.update', ['attendance_correct_request_id' => $application->id]) }}" method="POST">
          @csrf
          @method('PATCH')
          <x-black-button type="submit">承認</x-black-button>
        </form>
        @else
        <button type="button" class="c-btn-black c-button--approved" disabled>承認済み</button>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection