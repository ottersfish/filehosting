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
		{{ Form::model($user, array('method' => 'delete', 'url' => 'admin/'.$user->id)) }}
			<div class="row lead">
				<div class="col-md-12 col-xs-12">
					Are you sure want to delete <strong>{{ $user->username }}?</strong>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 col-xs-12">
					<div class="panel panel-default">
					<!-- Default panel contents -->
						<div class="panel-heading">List of {{ $user->username }} Files</div>

						<!-- Table -->
						<!-- <div class="panel-body"> -->
							<div class="table-responsive">
								<table class="table table-bordered table-stripped table-hover" id="dataTables">
									<thead>
										<tr>
											<td>#</td>
											<td>File Name</td>
											<td>Link</td>
											<td>File Size</td>
										</tr>
									</thead>
									<tbody>
									<?php
										$rownum=1;
										if(isset($files->username))$usernameFlag=1;
										else $usernameFlag=0;
									?>
										@foreach($files as $file)
										<?php
											$targetdir=public_path('files/'.$file->id_user.'/'.$file->key);
											$lists=scandir($targetdir,1);
											$filename=$lists[0];
											$filesize=filesize($targetdir.'/'.$filename);
											$bytes=$filesize;
											$precision=2;
											$units = array('B', 'KB', 'MB', 'GB', 'TB'); 
											$bytes = max($bytes, 0); 
											$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
											$pow = min($pow, count($units) - 1);
											$bytes /= (1 << (10 * $pow)); 
											$filesize=round($bytes, $precision) . ' ' . $units[$pow];
										?>
											<tr>
												<td>{{ $rownum++ }}</td>
												<td>{{ $filename }}</td>
												<td><a href="{{ url('home/download/'.$file->key) }}" >{{ url('home/download/'.$file->key) }}</a></td>
												<td>
													{{ $filesize }}
												</td>
												@if(!$usernameFlag)
												<td>
													{{ $file->username }}
												</td>
												@endif
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						<!-- </div> -->
					</div>
				</div>
			</div>
			{{ Form::submit("Delete", array("class"=>"btn btn-danger")) }}
		{{ Form::close() }}
	</div>
@stop