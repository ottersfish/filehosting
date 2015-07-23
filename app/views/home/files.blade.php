@extends('master')

@section('content')
	@if(Session::has('folderMessage'))
	<div class="row">
		<div class="col-md-6 col-xs-12">
			<p class="alert alert-success">
				{{ Session::get('folderMessage') }}
			</p>
		</div>
	</div>
	@endif
	<div class="row">
		<div class="col-md-6 col-xs-6">
			<div class="panel panel-info" >
				<div class="panel-heading">
					<p class="panel-title">
						Add Folder
					</p>
				</div>
				@if($errors->has())
					@foreach($errors->all() as $error)
						<div class="alert alert-danger">{{ $error }}</div>
					@endforeach
				@endif
				<div style="padding-top:30px" class="panel-body">
					{{Form::open(array('url' =>'home/addfolder'))}}
						<div class="row form-group">
							<label for="parent_folder" class="col-md-3 control-label">Parent Folder</label>
							{{ Form::select('parent', $folders, null, ['class' => 'col-md-8']) }}
						</div>
						<div class="row form-group">
							<label for="folder_name" class="col-md-3 control-label">Folder Name:</label>
							{{ Form::text('folder_name', null, ['class' => 'col-md-8']) }}
						</div>
						{{ Form::submit("Add", array('class' => 'btn btn-success btn-sm', 'type' => 'submit')) }}
					{{ Form::close() }}
				</div>
			</div>
		</div>
	</div>
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
				<div class="panel-heading">List of Your Files</div>

				<!-- Table -->
				<!-- <div class="panel-body"> -->
					<div class="table-responsive">
						<table class="table table-bordered table-stripped table-hover" id="dataTables">
							<thead>
								<tr>
									<td>#</td>
									<td>File Name</td>
									<td>Type</td>
									<td>Link</td>
									<td>File Size</td>
									<td colspan="2">Action</td>
									<td>Revisions</td>
								</tr>
							</thead>
							<tbody>
							<?php
								$rownum=1;
							?>
								@foreach($files as $file)
								<?php
									$targetdir = storage_path('files/'.Auth::user()->id.'/'.$file->key);
									$filename = $file->origFilename.'.'.$file->extension;
									$filesize = Helpers::formatFileSize(filesize($targetdir.'/'.$filename))
								?>
									<tr>
									<td>{{ $rownum++ }}</td>
									<td>{{ $file->filename }}</td>
									<td>{{ '.'.$file->extension }}</td>
									<td><a href="{{ url('home/download/'.$file->key) }}" >{{ url('home/download/'.$file->key) }}</a></td>
									<td>
										{{ $filesize }}
									</td>
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
		</div>
	</div>
@stop