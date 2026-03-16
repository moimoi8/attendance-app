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
    'date' => $date
    ])

    <x-attendance-table>
      <x-slot name="thead">
        <th class="attendance-table__header">名前</th>
        <th class="attendance-table__header">出勤</th>
        <th class="attendance-table__header">退勤</th>
        <th class="attendance-table__header">休憩</th>
        <th class="attendance-table__header">合計</th>
        <th class="attendance-table__header">詳細</th>
      </x-slot>

      @foreach($attendances as $attendance)
      <tr class="attendance-table__row">
        <td class="attendance-table__item">{{ $attendance->user->name }}</td>
        <td class="attendance-table__item">{{ $attendance->clock_in ? $attendance->clock_in->format('H:i') : '' }}</td>
        <td class="attendance-table__item">{{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '' }}</td>
        <td class="attendance-table__item">{{ $attendance->total_rest_time }}</td>
        <td class="attendance-table__item">{{ $attendance->total_work_time }}</td>
        <td class="attendance-table__item">
          <a href="{{ route('attendance.edit', ['id' => $attendance->id]) }}" class="attendance-table__link">詳細</a>
        </td>
      </tr>
      @endforeach
    </x-attendance-table>
  </div>
</div>
@endsection