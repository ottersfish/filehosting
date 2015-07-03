@extends('master')

@section('content')
	@if ($errors->has())
		@foreach ($errors->all() as $error)
			<div class='alert alert-danger'>{{ $error }}</div>
		@endforeach
	@endif
	@if (Session::has('message'))
		<div class="alert alert-success">{{ Session::get('message') }}</div>
	@endif

	{{ Form::open(array('role' => 'form', 'files'=>true, 'url' => 'home')) }}
	<div class="form-group">
		{{ Form::label('inputFile', 'File') }}
		{{ Form::file('userFile', null, ['class' => 'form-control']) }}
		<p class="help-block">Max file size @if(!Auth::check())1 @else 10 @endif MB.</p>
		{{ Form::submit('Upload', ['class' => 'btn btn-primary btn-sm']) }}
	</div>
	{{ Form::close() }}
@stop