@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="l-main-content">
  <div class="l-content-inner">
    @include('components.page-header', [
    'title' => '勤怠一覧',
    'showDateNav' => true
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
        <td class="attendance-table__item">山田 太郎</td>
        <td class="attendance-table__item">09：00</td>
        <td class="attendance-table__item">18：00</td>
        <td class="attendance-table__item">1：00</td>
        <td class="attendance-table__item">8：00</td>
        <td class="attendance-table__item">
          <a href="#" class="attendance-table__link">詳細</a>
        </td>
      </tr>
      @endforeach
    </x-attendance-table>
  </div>
</div>
@endsection