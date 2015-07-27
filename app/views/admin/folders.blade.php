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
		<div class="row">
			<div class="col-md-6 col-xs-6">
				<div class="panel panel-default">
				<!-- Default panel contents -->
					<div class="panel-heading">All of folders</div>

					<!-- Table -->
					<!-- <div class="panel-body"> -->
						<div class="table-responsive">
							<table class="table table-bordered table-stripped table-hover" id="dataTables">
								<thead>
									<tr>
										<td>#</td>
										<td>Parent</td>
										<td>Folder Name</td>
										<td>Owner</td>
									</tr>
								</thead>
								<tbody>
								<?php
									$rownum=1;
								?>
									@foreach($folders as $folder)
										<tr style="background-color:aliceblue">
											<td>{{ $rownum++ }}</td>
											<td>{{ $folder->parent }}</td>
											<td>{{ $folder->folder_name }}</td>
											<td>{{ $folder->username }}</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					<!-- </div> -->
				</div>
			</div>
		</div>
		@if(method_exists($folders, 'links')){{ $folders->links() }}@endif
	</div>
@stop