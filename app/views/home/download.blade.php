@extends('master')

@section('content')
	<div class="row lead">
		<div class="col-md-12 col-xs-12">
			You want to download file <strong>{{ $file->fileName }}({{ $file->fileSize }})</strong>.
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 col-xs-12">
			<a href="{{ url('home/do_download/'.$file->key) }}">
				<button class="btn btn-primary"><span class="glyphicon glyphicon-download-alt"></span> Download</button>
			</a>
		</div>
	</div>
@stop