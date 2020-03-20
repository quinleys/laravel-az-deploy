@extends('adminlte::page')

@section('title', 'Rooms Edit')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
        <h1>Edit {{$post['room']}} {{$post['id']}}</h1>
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
           Edit  {{$post['room']}} {{$post['id']}}
           </li>
           </ol>
        </div>
    </div>
</div>

@stop

@section('content')
<form action="{{ route('rooms.update', $post['room']) }} " method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
  <div class="form-group">
    <label for="description">Description</label>
    <textarea class="form-control" name="description" id="exampleFormControlTextarea1" >{{$post['description']}}</textarea>
  </div>
    <button type="submit" class="btn btn-primary">Submit</button>
    <a href="/rooms" class="btn btn-danger">Cancel </a>
</form>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log(); </script>
@stop