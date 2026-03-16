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
            <span class="attendance-detail__value attendance-detail__year">
              {{ $application->attendance->date->format('Y年') }}</span>
            <span class="attendance-detail__separator"></span>
            <span class="attendance-detail__value attendance-detail__date">
              {{ $application->attendance->date->format('n月j日') }}</span>
          </div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--auto">
          <label class="attendance-detail__label">出勤・退勤</label>
          <div class="attendance-detail__content">
            <span class="attendance-detail__value">
              {{ optional($application->requested_clock_in)->format('H:i') }}
            </span>
            <span class="attendance-detail__separator">～</span>
            <span class="attendance-detail__value">
              {{ optional($application->requested_clock_out)->format('H:i') }}
            </span>
          </div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--auto">
          <label class="attendance-detail__label">休憩</label>
          <div class="attendance-detail__content">
            @php $rest1 = $application->attendance->rests->get(0); @endphp
            <span class="attendance-detail__value">
              {{ $rest1 ? $rest1->start_time->format('H:i') : '' }}
            </span>
            <span class="attendance-detail__separator">～</span>
            <span class="attendance-detail__value">
              {{ $rest1 && $rest1->end_time ? $rest1->end_time->format('H:i') : '' }}
            </span>
          </div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--auto">
          <label class="attendance-detail__label">休憩2</label>
          <div class="attendance-detail__content">
            @php $rest2 = $application->attendance->rests->get(1); @endphp
            <span class="attendance-detail__value">
              {{ $rest2 ? $rest2->start_time->format('H:i') : '' }}
            </span>
            <span class="attendance-detail__separator">～</span>
            <span class="attendance-detail__value">
              {{ $rest2 && $rest2->end_time ? $rest2->end_time->format('H:i') : '' }}
            </span>
          </div>
        </div>

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