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
		@unless($method == 'delete')
		<div id="" class="mainbox col-md-6 col-sm-8">
			<div class="panel panel-info" >
				<div class="panel-heading">
					<p class="panel-title">
						Edit file
					</p>
				</div>
				<div style="padding-top:30px" class="panel-body">
					{{Form::model($file, array('method' => $method, 'url'=>'home/edit/'.$file->key))}}
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
		@else
			{{ Form::model($file, array('method' => $method, 'url' => 'home/edit/'.$file->key)) }}
				<div class="row lead">
					<div class="col-md-12 col-xs-12">
						Are you sure want to delete file <strong>{{ $file->fileName }}({{ $file->fileSize }})?</strong>.
					</div>
				</div>
				{{ Form::submit("Delete", array("class"=>"btn btn-danger")) }}
			{{ Form::close() }}
		@endif
	</div>
@stop