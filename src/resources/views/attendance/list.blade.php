@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="l-main-content">
  <div class="l-content-inner">
    @include('components.page-header', [
    'title' => '勤怠一覧',
    'showDateNav' => true,
    'navType' => 'month',
    'date' => $date,
    'routeName' => 'attendance.list'
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
      $pending = $attendance?->correctRequest;
      $isPending = ($pending && $pending->status == 1);
      $clockIn = $isPending ? \Carbon\Carbon::parse($pending->requested_clock_in)->format('H:i') : $attendance?->clock_in?->format('H:i');
      $clockOut = $isPending ? \Carbon\Carbon::parse($pending->requested_clock_out)->format('H:i') : $attendance?->clock_out?->format('H:i');

      @endphp

      <tr class="attendance-table__row">
        <td class="attendance-table__item">{{ $day->isoFormat('MM/DD(ddd)') }}</td>
        <td class="attendance-table__item">{{ $clockIn ?? '' }}</td>
        <td class="attendance-table__item">{{ $clockOut ?? '' }}</td>
        <td class="attendance-table__item">{{ $attendance?->total_rest_time ?? '' }}</td>
        <td class="attendance-table__item">{{ $attendance?->total_work_time ?? '' }}</td>
        <td class="attendance-table__item">

          <a href="{{ route('attendance.edit', ['id' => $attendance->id ?? 0, 'date' => $day->format('Y-m-d')]) }}" class="attendance-table__link">詳細</a>
        </td>
      </tr>
      @endforeach
    </x-attendance-table>
  </div>
</div>
@endsection