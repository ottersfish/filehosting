@extends('master')

@section('content')
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <p class="alert alert-success">Your file was successfully uploaded!</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <p class="lead">You can download your file using this link:</p>
            <span class="glyphicon glyphicon-file"></span>
            <!-- link_to_route('files.download', route('files.download', array('key' => $key)), array('key' => $key)) -->
            <a href="{{ route('files.show', array('key' => $key)) }}" style="word-wrap: break-word; white-space: normal;">
                {{ route('files.show', array('key' => $key)) }}
            </a>
        </div>
    </div>

@stop