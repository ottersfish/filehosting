@extends('master')

@section('content')
	{{ Form::open(array('method' => 'post')) }}
	{{ Form::submit("Edit", array('class' => 'btn btn-success btn-sm', 'type' => 'submit')) }}
	{{ Form::close() }}
@stop