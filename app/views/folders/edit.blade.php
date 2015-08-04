@extends('master')

@section('content')
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
                        Edit Folder
                    </p>
                </div>
                @if($errors->has())
                    @foreach($errors->all() as $error)
                        <div class="alert alert-danger">{{ $error }}</div>
                    @endforeach
                @endif
                <div style="padding-top:30px" class="panel-body">
                    {{Form::open(array('method' => $method, 'url'=>route('folders.update', array('key' => $folder->key))))}}
                    <div class="row form-group">
                        {{ Form::label('folder_name', 'You\'re about to edit: '.$folder->folder_name, ['class' => 'col-md-12']) }}
                        {{ Form::label('folder_name', null, ['class' => 'col-md-3'])}}
                        {{ Form::text('folder_name', null, ['class' => 'col-md-8']) }}
                    </div>
                    <p class="help-block">Please input your desired folder name to create inside the chosen parent folder.</p>
                    {{ Form::submit("Edit", array('class' => 'btn btn-success btn-sm', 'type' => 'submit')) }}
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop