@extends('adminlte::page')

@section('title', 'Who is who Dashboard')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
        <h1>Edit {{$post['id']}}</h1>
        </div>
        <div class="col-sm-6">
           <ol class="breadcrumb float-sm-right">
           <li class="breadcrumb-item">
           <a href="/home">Home</a>
           </li>
           <li class="breadcrumb-item">
           <a href="/general">General</a>
           </li>
           <li class="breadcrumb-item active">
           Edit {{$post['id']}}
           </li>
           </ol>
        </div>
    </div>
</div>

@stop

@section('content')
<p>Currently uploaded: {{$post['audio']}} </p>
<form action="{{ route('general.update',$post['id']) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
    <label for="exampleFormControlFile1">Audio input</label>
    <input type="file" name="audio" class="form-control-file" id="exampleFormControlFile1">
  </div>
    <button type="submit" class="btn btn-primary">Submit</button>
    <a href="/general" class="btn btn-danger">Cancel </a>
</form>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log(); </script>
@stop