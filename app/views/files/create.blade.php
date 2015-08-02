@extends('master')

@section('content')
    @if($errors->has())
        <div class="row" style="margin-bottom:10px;">
            <div class="col-md-6 col-xs-6"> 
            @foreach ($errors->all() as $error)
                <div class='alert alert-danger'>{{ $error }}</div>
            @endforeach
            </div>
        </div>
    @endif
    @if(Session::has('message'))
        <div class="row" style="margin-bottom:10px;">
            <div class="col-md-6 col-xs-6"> 
                <div class="alert alert-success">{{ Session::get('message') }}</div>
            </div>
        </div>
    @endif

    <div class="col-md-6 col-xs-6"> 
        {{ Form::open(array('role' => 'form', 'files'=>true, 'url' => route('files.store'))) }}
        <div class="form-group">
            @if(Auth::check())
                <div class="row form-group">
                    <label for="parent_folder" class="col-md-3 control-label">Upload to:</label>
                    {{ Form::select('folder', $folders, null, ['class' => 'col-md-8']) }}
                </div>
            @endif
            {{ Form::label('inputFile', 'File') }}
            {{ Form::file('userFile', null, ['class' => 'form-control']) }}
            <p class="help-block">Max file size @if(!Auth::check())1 @else 10 @endif MB.</p>
            {{ Form::submit('Upload', ['class' => 'btn btn-primary btn-sm']) }}
        </div>
        {{ Form::close() }}
    </div>
@stop