@extends('master')

@section('navbar')
    <li><a href="{{ url('/') }}"><span class="glyphicon glyphicon-home"></span> Home</a></li>
    <li><a href="{{ route('admin.folders.index') }}"><span class="glyphicon glyphicon-folder-open"></span> Folders</a></li>
    <li><a href="{{ route('admin.files.index') }}"><span class="glyphicon glyphicon-file"></span> Files</a></li>
    <li><a href="{{ route('admin.users.index') }}"><span class="glyphicon glyphicon-user"></span> Users</a></li>
    <li><a href="{{ route('admin.logs.index') }}"><span class="glyphicon glyphicon-list"></span> Logs</a></li>
@stop