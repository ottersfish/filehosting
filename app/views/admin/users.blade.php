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
				<div class="panel-heading">List of Users</div>

				<!-- Table -->
				<!-- <div class="panel-body"> -->
					<div class="table-responsive">
						<table class="table table-bordered table-stripped table-hover" id="dataTables">
							<thead>
								<tr>
									<td>#</td>
									<td>User Id</td>
									<td>Email</td>
									<td>Username</td>
									<td>Name</td>
									<td colspan="2">Action</td>
								</tr>
							</thead>
							<tbody>
							<?php
								$rownum=1;
							?>
								@foreach($users as $user)
								<?php
								?>
									<tr>
										<td>{{ $rownum++ }}</td>
										<td>{{ $user->id }}</td>
										<td>{{ $user->email }}</td>
										<td>{{ $user->username }}</td>
										<td>{{ $user->name }}</td>
										<td>
											<a href="{{ url('admin/users/'.$user->id.'/delete') }}">
												{{ Form::button('Delete', ['class' => 'btn btn-danger btn-sm']) }}
											</a>
										</td>
										<td>
											<a href="{{ url('admin/files/'.$user->id) }}">
												{{ Form::button('Files', ['class' => 'btn btn-primary btn-sm'])}}
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