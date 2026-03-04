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

    @include('components.attendance-table', [
    'first_colum_label' => '日付',
    'items' => $staff__attendances
    ])

  </div>
</div>
@endsection