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

        <div class="attendance-detail__row attendance-detail__row--auto">
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
            <span class="attendance-detail__value">09:00</span>
            <span class="attendance-detail__separator">～</span>
            <span class="attendance-detail__value">18:00</span>
          </div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--auto">
          <label class="attendance-detail__label">休憩</label>
          <div class="attendance-detail__content">
            <span class="attendance-detail__value">12:00</span>
            <span class="attendance-detail__separator">～</span>
            <span class="attendance-detail__value">13:00</span>
          </div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--auto">
          <label class="attendance-detail__label">休憩2</label>
          <div class="attendance-detail__content">
            <span class="attendance-detail__value">15:00</span>
            <span class="attendance-detail__separator">～</span>
            <span class="attendance-detail__value">15:10</span>
          </div>
        </div>

        <div class="attendance-detail__row attendance-detail__row--note">
          <label class="attendance-detail__label">備考</label>
          <div class="attendance-detail__content">
            <p class="attendance-detail__text">電車遅延のため</p>
          </div>
        </div>

        <div class="attendance-detail__actions">
          <x-black-button type="submit">承認</x-black-button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection