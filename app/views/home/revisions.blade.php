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
								</tr>
							</thead>
							<tbody>
							<?php
								$rownum=1;
							?>
								@foreach($revHistory as $hist)
									<tr>
										<td>{{ $rownum++ }}</td>
										<td>{{ $hist->timestamp }}</td>
										<td>{{ $hist->uploadedFileName }}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				<!-- </div> -->
			</div>
		</div>
	</div>
	<div id="" class="mainbox col-md-6 col-sm-8">
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
@stop