@extends('master')

@section('content')
	@if(Session::has('message'))
	<div class="row">
		<div class="col-md-6 col-xs-12">
			<p class="alert alert-success">
				{{ Session::get('message') }}
			</p>
		</div>
	</div>
	@endif
	<div class="row">
		<div class="col-md-12 col-xs-12">
			<div class="panel panel-default">
			<!-- Default panel contents -->
				<div class="panel-heading">List of @if(isset($files->username)){{$files->username}}@else {{'All'}}@endif Files</div>
				<!-- Table -->
				<!-- <div class="panel-body"> -->
					<div class="table-responsive">
						<table class="table table-bordered table-stripped table-hover" id="dataTables">
							<thead>
								<tr>
									<td>#</td>
									<td>File Name</td>
									<td>Extension</td>
									<td>Link</td>
									<td>File Size</td>
									@if(!isset($files->username))<td>Owner</td>@endif
									<td colspan="2">Action</td>
									<td>Revisions</td>
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
									$targetdir=storage_path('files/'.$file->id_user.'/'.$file->key);
									$filename = $file->origFilename.'.'.$file->extension;
									$filesize = Helpers::formatFileSize(filesize($targetdir.'/'.$filename));
								?>
									<tr>
										<td>{{ $rownum++ }}</td>
										<td>{{ $file->filename }}</td>
										<td>{{ '.'.$file->extension }}</td>
										<td><a href="{{ url('home/download/'.$file->key) }}" >{{ url('home/download/'.$file->key) }}</a></td>
										<td>
											{{ $filesize }}
										</td>
										@if(!$usernameFlag)
										<td>
											{{ $file->username }}
										</td>
										@endif
										<td>
											<a href="{{ url('home/edit/'.$file->key.'/delete') }}">
												{{ Form::button('Delete', ['class' => 'btn btn-danger btn-sm'])}}
											</a>	
										</td>
										<td>
											<a href="{{ url('home/edit/'.$file->key.'/edit') }}">
												{{ Form::button('Edit', ['class' => 'btn btn-success btn-sm'])}}
											</a>
										</td>
										<td>
											<a href="{{ url('home/edit/'.$file->key.'/revision') }}">
												{{ Form::button('Show', ['class' => 'btn btn-default btn-sm']) }}
											</a>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				<!-- </div> -->
			</div>
			@if(method_exists($files, 'links')){{ $files->links() }}@endif
		</div>
	</div>
@stop