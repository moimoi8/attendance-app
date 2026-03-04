@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="l-main-content">
  <div class="l-content-inner">

    @include('components.page-header', [
    'title' => \Carbon\Carbon::parse($date)->format('y年n月j日') . 'の勤怠',
    'showDateNav' => true
    'date' => $date
    ])

    @include('components.attendance-table', [
    'first_colum_label' => '名前',
    'items' => $all_staff__attendances
    ])

  </div>
</div>
@endsection