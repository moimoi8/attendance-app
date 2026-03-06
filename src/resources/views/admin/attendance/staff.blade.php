@extends('layouts.app')

@section('content')
<div class="l-main-content">
  <div class="l-content-inner">
    @include('components.page-header', [
    'title' => $user->name . 'さんの勤怠',
    'showDateNav' => true,
    'date' => $date
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

      @foreach($attendances as $attendance)
      <tr class="attendance-table__row">
        <td class="attendance-table__item">{{ $attendance->date }}</td>

        @if($attendance->start_time)
        <td class="attendance-table__item">{{ $attendance->start_time }}</td>
        <td class="attendance-table__item">{{ $attendance->end_time }}</td>
        <td class="attendance-table__item">{{ $attendance->total_rest_time }}</td>
        <td class="attendance-table__item">{{ $attendance->total_work_time }}</td>
        @else
        <td class="attendance-table__item"></td>
        <td class="attendance-table__item"></td>
        <td class="attendance-table__item"></td>
        <td class="attendance-table__item"></td>
        @endif

        <td class="attendance-table__item">
          <a href="#" class="attendance-table__link">詳細</a>
        </td>
      </tr>
      @endforeach
    </x-attendance-table>

    <div class="attendance-detail__actions">
      <x-black-button type="button">CSV出力</x-black-button>
    </div>
  </div>
</div>
@endsection