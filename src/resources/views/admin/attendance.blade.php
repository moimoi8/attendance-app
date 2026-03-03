@extends('layout.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
@endsection

@section('content')
@include('components.page-header', [
'title' => '2023年6月1日の勤怠',
'showDateNav' => true
])