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

    <form class="attendance-detail__form" action="{{ route('admin.approve.update', ['attendance_correct_request_id' => $application->id]) }}" method="POST">
      @csrf
      @method('PATCH')
      <div class="attendance-detail__table">
        <div class="attendance-detail__row attendance-detail__row--name">
          <label class="attendance-detail__label">名前</label>
          <div class="attendance-detail__content">{{ $application->attendance->user->name }}</div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--label">
          <label class="attendance-detail__label">日付</label>
          <div class="attendance-detail__content">
            <span class="attendance-detail__value">{{ $application->attendance->date->format('Y年') }}</span>
            <span class="attendance-detail__separator"></span>
            <span class="attendance-detail__value">{{ $application->attendance->date->format('n月j日') }}</span>
          </div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--auto">
          <label class="attendance-detail__label">出勤・退勤</label>
          <div class="attendance-detail__content">
            <input type="text" name="clock_in" class="attendance-detail__input attendance-detail__value" value="{{ optional($application->requested_clock_in)->format('H:i') }}">
            <span class="attendance-detail__separator">～</span>
            <input type="text" name="clock_out" class="attendance-detail__input attendance-detail__value" value="{{ optional($application->requested_clock_out)->format('H:i') }}">
          </div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--auto">
          <label class="attendance-detail__label">休憩</label>
          <div class="attendance-detail__content">
            @php $rest1 = $application->attendance->rests->get(0); @endphp
            <input type="text" name="rest1_start" class="attendance-detail__input attendance-detail__value" value="{{ $rest1 ? $rest1->start_time->format('H:i') : '' }}">
            <span class="attendance-detail__separator">～</span>
            <input type="text" name="rest1_end" class="attendance-detail__input attendance-detail__value" value="{{ $rest1 && $rest1->end_time ? $rest1->end_time->format('H:i') : '' }}">
          </div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--auto">
          <label class="attendance-detail__label">休憩2</label>
          <div class="attendance-detail__content">
            @php $rest2 = $application->attendance->rests->get(1); @endphp
            <input type="text" name="rest2_start" class="attendance-detail__input attendance-detail__value" value="{{ $rest2 ? $rest2->start_time->format('H:i') : '' }}">
            <span class="attendance-detail__separator">～</span>
            <input type="text" name="rest2_end" class="attendance-detail__input attendance-detail__value" value="{{ $rest2 && $rest2->end_time ? $rest2->end_time->format('H:i') : '' }}">
          </div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--note">
          <label class="attendance-detail__label">備考</label>
          <div class="attendance-detail__content">
            <textarea name="description" class="attendance-detail__textarea">{{ $application->attendance->reason }}</textarea>
          </div>
        </div>
      </div>

      <div class="attendance-detail__actions">
        @if($application->attendance->status == 1)
        <form action="{{ route('admin.approve.update', ['attendance_correct_request_id' => $application->id]) }}" method="POST">
          @csrf
          <x-black-button type="submit">承認</x-black-button>
        </form>
        @else
        <button type="button" class="c-btn-black c-button--approved" disabled>承認済み</button>
        @endif
      </div>
    </form>
  </div>
</div>
@endsection