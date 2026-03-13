@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/application.css') }}">
@endsection

@section('content')
<div class="l-main-content">
  <div class="l-content-inner">
    @include('components.page-header', [
    'title' => '申請一覧',
    ])

    <div class="attendance-tabs">
      <ul class="attendance-tabs__list">
        <li class="attendance-tabs__item">
          <a href="{{ route('admin.approve.list', ['tab' => 'pending']) }}" class="attendance-tabs__link {{ request('tab') != 'approved' ? 'is-active' : '' }}">承認待ち</a>
        </li>
        <li class="attendance-tabs__item">
          <a href="{{ route('admin.approve.list', ['tab' => 'approved']) }}" class="attendance-tabs__link {{ request('tab') == 'approved' ? 'is-active' : '' }}">承認済み</a>
        </li>
      </ul>
    </div>

    <x-attendance-table>
      <x-slot name="thead">
        <th class="attendance-table__header">状態</th>
        <th class="attendance-table__header">名前</th>
        <th class="attendance-table__header">対象日時</th>
        <th class="attendance-table__header">申請理由</th>
        <th class="attendance-table__header">申請日時</th>
        <th class="attendance-table__header">詳細</th>
      </x-slot>

      @foreach($applications as $application)
      <tr class="attendance-table__row">
        <td class="attendance-table__item">{{ $application->status == 1 ? '承認待ち' : '承認済み' }}</td>
        <td class="attendance-table__item">{{ $application->user->name }}</td>
        <td class="attendance-table__item">{{ $application->attendance->date->format('Y/m/d') }}</td>
        <td class="attendance-table__item">{{ $application->reason }}</td>
        <td class="attendance-table__item">{{ $application->created_at->format('Y-m-d') }}</td>
        <td class="attendance-table__item">
          <a href="{{ route('admin.approve.request_detail', ['attendance_correct_request_id' => $application->id]) }}" class="attendance-table__link">詳細</a>
        </td>
      </tr>
      @endforeach
    </x-attendance-table>
  </div>
</div>
@endsection