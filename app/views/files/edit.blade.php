@extends('master')

@section('content')
    @if($errors->has())
        @foreach ($errors->all() as $error)
            <div class='alert alert-danger'>{{ $error }}</div>
        @endforeach
    @endif
    @if(Session::has('message'))
    <div class="row">
        <div class="col-md-6 col-sm-8">
            <p class="alert alert-success">
                {{ @Session::get('message') }}
            </p>
        </div>
    </div>
    @endif

    <div class="row">
        <div id="" class="mainbox col-md-6 col-sm-8">
            <div class="panel panel-info" >
                <div class="panel-heading">
                    <p class="panel-title">
                        Edit file
                    </p>
                </div>
                <div style="padding-top:30px" class="panel-body">
                    {{Form::model($file, array('method' => $method, 'url'=>route('files.update', array('key' => $file->key))))}}
                    <div class="row form-group">
                        <label for="fileName" class="col-md-3 control-label">File Name:</label>
                        {{ Form::text('fileName', null, ['class' => 'col-md-8']) }}
                    </div>
                    <div class="row form-group">
                        <label for="extension" class="col-md-3 control-label">Extension:</label>
                        {{ Form::text('extension', null, ['class' => 'col-md-8', 'disabled' => 'true']) }}
                    </div>
                    <div class="row form-group">
                        <label for="path" class="col-md-3 control-label">Folder:</label>
                        {{ Form::text('path', null, ['class' => 'col-md-8', 'disabled' => 'true']) }}
                    </div>
                    {{ Form::submit("Edit", array('class' => 'btn btn-success btn-sm', 'type' => 'submit')) }}
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div id="" class="mainbox col-md-6 col-sm-8">
            <div class="panel panel-info" >
                <div class="panel-heading">
                    <p class="panel-title">
                        Move file
                    </p>
                </div>
                <div style="padding-top:30px" class="panel-body">
                    <div class="row form-group">
                        <label for="cur_folder" class="col-md-3 control-label">Current Folder:</label>
                        {{ $cur_folder }}
                    </div>
                    {{Form::open(array('method' => $method, 'url'=>route('files.move-folder',array('key' => $file->key))))}}
                        <div class="row form-group">
                            <label for="folder" class="col-md-3 control-label">Move to:</label>
                            {{ Form::select('folder', $folders, null, ['class' => 'col-md-8']) }}
                        </div>
                        {{ Form::submit("Move", array('class' => 'btn btn-success btn-sm', 'type' => 'submit')) }}
                    {{ Form::close() }}
                </div>
            </div>  
        </div>
    </div>
@stop