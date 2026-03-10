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
          <a href="#" class="attendance-tabs__link is-active">承認待ち</a>
        </li>
        <li class="attendance-tabs__item">
          <a href="#" class="attendance-tabs__link">承認済み</a>
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
        <td class="attendance-table__item">承認待ち</td>
        <td class="attendance-table__item">西 怜奈</td>
        <td class="attendance-table__item">2023/06/01</td>
        <td class="attendance-table__item">遅延のため</td>
        <td class="attendance-table__item">2023/06/02</td>
        <td class="attendance-table__item">
          <a href="#" class="attendance-table__link">詳細</a>
        </td>
      </tr>
      @endforeach
    </x-attendance-table>
  </div>
</div>
@endsection