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

    <form class="attendance-detail__form">
      <div class="attendance-detail__table">
        <div class="attendance-detail__row attendance-detail__row--name">
          <label class="attendance-detail__label">名前</label>
          <div class="attendance-detail__content">西 怜奈</div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--label">
          <label class="attendance-detail__label">日付</label>
          <div class="attendance-detail__content">
            <span class="attendance-detail__value">2023年</span>
            <span class="attendance-detail__separator"></span>
            <span class="attendance-detail__value">6月1日</span>
          </div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--auto">
          <label class="attendance-detail__label">出勤・退勤</label>
          <div class="attendance-detail__content">
            <input type="text" class="attendance-detail__input attendance-detail__value" value="09:00">
            <span class="attendance-detail__separator">～</span>
            <input type="text" class="attendance-detail__input attendance-detail__value" value="20:00">
          </div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--auto">
          <label class="attendance-detail__label">休憩</label>
          <div class="attendance-detail__content">
            <input type="text" class="attendance-detail__input attendance-detail__value" value="09:00">
            <span class="attendance-detail__separator">～</span>
            <input type="text" class="attendance-detail__input attendance-detail__value" value="20:00">
          </div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--auto">
          <label class="attendance-detail__label">休憩2</label>
          <div class="attendance-detail__content">
            <input type="text" class="attendance-detail__input attendance-detail__value" value="09:00">
            <span class="attendance-detail__separator">～</span>
            <input type="text" class="attendance-detail__input attendance-detail__value" value="20:00">
          </div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--note">
          <label class="attendance-detail__label">備考</label>
          <div class="attendance-detail__content">
            <textarea class="attendance-detail__textarea">電車遅延のため</textarea>
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