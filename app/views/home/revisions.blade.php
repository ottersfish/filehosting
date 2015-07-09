@extends('master')

@section('content')
	<div class="row">	
		<div class="col-md-12 col-xs-12">
			<div class="panel panel-default">
			<!-- Default panel contents -->
				<div class="panel-heading">Revision History</div>

				<!-- Table -->
				<!-- <div class="panel-body"> -->
					<div class="table-responsive">
						<table class="table table-bordered table-stripped table-hover" id="dataTables">
							<thead>
								<tr>
									<td>#</td>
									<td>Timestamp</td>
									<td>Uploaded File Name</td>
									<td>File Name</td>
									<td>Extension</td>
									<td>Active</td>
								</tr>
							</thead>
							<tbody>
							<?php
								$rownum=1;
							?>
								@foreach($revHistory as $hist)
									<?php
										$hist->origFilename = substr($hist->origFilename, 20);
									?>
									<tr>
										<td>{{ $rownum++ }}</td>
										<td>{{ $hist->created_at }}</td>
										<td>{{ $hist->origFilename }}</td>
										<td>{{ $hist->filename }}</td>
										<td>{{ '.'.$hist->extension }}</td>
										@if($hist->is_active)
											<td>
												<button class="btn btn-sm btn-success">Active</button>
											</td>
										@else
											<td>
												<a href="{{ url('home/edit/'.$hist->key.'/setactive/'.$hist->id) }}">
													<button class="btn btn-sm btn-default">Set</button>
												</a>
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
		<div class="mainbox col-md-6 col-sm-8">
			<div class="panel panel-info" >
				<div class="panel-heading">
					<p class="panel-title">
						Upload Revision
					</p>
				</div>
				@if($errors->has())
					@foreach($errors->all() as $error)
						<div class="alert alert-danger">{{ $error }}</div>
					@endforeach
				@endif
				<div style="padding-top:30px" class="panel-body">
					{{ Form::open(array('role' => 'form', 'files'=>true, 'url' => 'home/edit/'.$file->key.'/revision')) }}
					<div class="form-group">
						{{ Form::file('userFile', null, ['class' => 'form-control']) }}
						<p class="help-block">Max file size @if(!Auth::check())1 @else 10 @endif MB.</p>
						{{ Form::submit('Revise', ['class' => 'btn btn-primary btn-sm']) }}
					</div>
					{{ Form::close() }}
				</div>
			</div>
		</div>
	</div>
@stop