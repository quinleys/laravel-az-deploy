@extends('adminlte::page')

@section('title', 'Rooms Dashboard')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>{{$kamer ?? 'Rooms'}}  Dashboard</h1>
        </div>
        <div class="col-sm-6">
           <ol class="breadcrumb float-sm-right">
           <li class="breadcrumb-item">
           <a href="/home">Home</a>
           </li>
           <li class="breadcrumb-item">
           <a href="/rooms">Rooms</a>
           </li>
           <li class="breadcrumb-item active">
           {{$kamer}}
           </li>
           </ol>
        </div>
    </div>
</div>
@stop

@section('content')
    <div class="box">
        <div class="box-header">
        </div>
        <div class="box-body">
        <table id="example1" class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>spot</th>
            <th>description</th>
            <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($all_post as $post)
            <tr>
            <td>{{$post['spot']}}</td>
                <td>{{$post['description']}}</td>
                <td>
                <a class="btn btn-primary" href="/rooms/{{$post['id']}}/edit" role="button">Edit</a>
                </td>
            </tr>
            @endforeach
            </tbody>
            </table>
    </div>

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log(); </script>
@stop