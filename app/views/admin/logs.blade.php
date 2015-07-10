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
									<td>id</td>
									<td>Table Affected</td>
									<td>Column Affected</td>
									<td>Action</td>
									<td>Old Value</td>
									<td>New Value</td>
									<td>User</td>
									<td>Timestamp</td>
								</tr>
							</thead>
							<tbody>
							<?php $rownum=1; ?>
								@foreach($logs as $log)
									<tr>
										<td>{{ $rownum++ }}</td>
										<td>{{ $log->table_affected }}</td>
										<td>{{ $log->column_affected }}</td>
										<td>
											@if($log->old_value){{ $log->old_value }}
											@else {{ '-' }}
											@endif
										</td>
										<td>
											@if($log->new_value){{ $log->new_value }}
											@else {{ '-' }}
											@endif
										</td>
										<td>{{ $log->action }}</td>
										<td>{{ $log->username }}</td>
										<td>{{ $log->created_at }}</td>
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