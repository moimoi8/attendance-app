@extends('layouts.app')

@section('content')
<div class="l-main-content">
  <div class="l-content-inner">
    @include('components.page-header', [
    'title' => 'スタッフ一覧',
    ])

    <x-attendance-table>
      <x-slot name="thead">
        <th class="attendance-table__header">名前</th>
        <th class="attendance-table__header">メールアドレス</th>
        <th class="attendance-table__header">月次勤怠</th>
      </x-slot>

      @foreach($users as $user)
      <tr class="attendance-table__row">
        <td class="attendance-table__item">{{ $user->name }}</td>
        <td class="attendance-table__item">{{ $user->email }}</td>
        <td class="attendance-table__item">
          <a href="#" class="attendance-table__link">詳細</a>
        </td>
      </tr>
      @endforeach
    </x-attendance-table>
  </div>
</div>
@endsection