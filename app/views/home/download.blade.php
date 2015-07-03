@extends('master')

@section('content')
	<?php
		// $file=$file[0];
		// if(Auth::check()){
		// 	$id=Auth::user()->id;
		// }
		// else{
		// 	$id=2;
		// }
		// $targetdir='files/'.$id.'/'.$file->key;
		// $lists=scandir($targetdir,1);
		// $filename=$lists[0];
		// $filesize=filesize($targetdir.'/'.$filename);

		// $bytes=$filesize;
		// $precision=2;
		// $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
		// $bytes = max($bytes, 0); 
		// $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
		// $pow = min($pow, count($units) - 1);
		// $bytes /= (1 << (10 * $pow)); 
		// $filesize=round($bytes, $precision) . ' ' . $units[$pow];
	?>
	<div class="row lead">
		<div class="col-md-12 col-xs-12">
			You want to download file <strong>{{ $file->filename }}({{ $file->filesize }})</strong>.
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