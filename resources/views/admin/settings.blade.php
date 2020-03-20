@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
<div class="row">
  <div class="col-12">
  <div class="card mb-3">
  <div class="card-header border">
  <h3 class="card-title">User info</h3>
  </div>
  <ul class="list-group list-group-flush">
    <li class="list-group-item">email: {{$user['email']}}</li>
    <li class="list-group-item">name: {{$user['name']}}</li>
    <li class="list-group-item">    <a class="btn btn-primary" href="{{ route('password.request') }}">
                                        {{ __('Change Password') }}
    </a></li>
  </ul>
</div>

  </div>

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log(); </script>
@stop