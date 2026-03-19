@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="l-main-content">
  <div class="l-content-inner">
    @include('components.page-header', [
    'title' => $user->name . 'さんの勤怠',
    'showDateNav' => true,
    'navType' => 'month',
    'date' => $date,
    'routeName' => 'admin.attendance.staff'
    ])

    <x-attendance-table>
      <x-slot name="thead">
        <th class="attendance-table__header">日付</th>
        <th class="attendance-table__header">出勤</th>
        <th class="attendance-table__header">退勤</th>
        <th class="attendance-table__header">休憩</th>
        <th class="attendance-table__header">合計</th>
        <th class="attendance-table__header">詳細</th>
      </x-slot>

      @foreach($calendarDays as $day)
      @php
      $currentDayStr = $day->format('Y-m-d');
      $attendance = $attendances->get($currentDayStr);
      @endphp

      <tr class="attendance-table__row">
        <td class="attendance-table__item">{{ $day->isoFormat('MM/DD(ddd)') }}</td>
        <td class="attendance-table__item">{{ $attendance?->clock_in?->format('H:i') ?? '' }}</td>
        <td class="attendance-table__item">{{ $attendance?->clock_out?->format('H:i') ?? '' }}</td>
        <td class="attendance-table__item">{{ $attendance?->total_rest_time ?? '' }}</td>
        <td class="attendance-table__item">{{ $attendance?->total_work_time ?? '' }}</td>
        <td class="attendance-table__item">

          <a href="{{ route('attendance.edit', ['id' => $attendance->id ?? 0, 'date' => $day->format('Y-m-d')]) }}" class="attendance-table__link">詳細</a>
        </td>
      </tr>
      @endforeach
    </x-attendance-table>

    <div class="attendance-detail__actions">
      <a href="{{ route('admin.staff.export', ['id' => $user->id, 'month' => \Carbon\Carbon::parse($date)->format('Y-m')]) }}">
        <x-black-button type="button">CSV出力</x-black-button>
      </a>
    </div>
  </div>
</div>
@endsection