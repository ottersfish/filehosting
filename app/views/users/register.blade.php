@extends('master')

@section('content')
	<div id="signupbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">                    
		<div class="panel panel-info" >
			<div class="panel-heading">
				<p class="panel-title">
					Sign Up
				</p>
			</div>
			@if($errors->has())
				@foreach($errors->all() as $error)
					<div class="alert alert-danger">{{ $error }}</div>
				@endforeach
			@endif
			<div style="padding-top:30px" class="panel-body">
				{{ Form::open(array('id' => 'register_form', 'class' => 'form-horizontal', 'url' => route('users.store'))) }}
					<div class="row form-group">
						<label for="email" class="col-md-3 control-label">Your Email:</label>
						<div class="col-md-4">
							{{ Form::text('email') }}
						</div>
					</div>
					<div class="row form-group">
						<label for="user_name" class="col-md-3 control-label">User Name:</label>
						<div class="col-md-4">
							{{ Form::text('username', null) }}
						</div>
					</div>
					<div class="row form-group">
						<label for="password" class="col-md-3 control-label">Password:</label>
						<div class="col-md-4">
							{{ Form::password('password') }}
						</div>
					</div>
					<div class="row form-group">
						<label for="con_password" class="col-md-3 control-label">Confirm Password:</label>
						<div class="col-md-4">
							{{ Form::password('con_password') }}
						</div>
					</div>
					<div class="row form-group">
						<label for="nama" class="col-md-3 control-label">Name:</label>
						<div class="col-md-4">
							{{ Form::text('name') }}
						</div>
					</div>
					{{ Form::button("Sign Up", array('class' => 'btn btn-success btn-sm', 'type' => 'submit')) }}
				{{ Form::close() }}
			</div>
			<div class="panel-footer"><p class="panel-help">And enjoy the easiest hosting ever!</p></div>
		</div>
	</div>
@stop