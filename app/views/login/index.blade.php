@extends('master')

@section('content')

	<div id="full_loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
		<div class="panel panel-info" >
			<div class="panel-heading">
				<div class="panel-title">Log In</div>
			</div>
			@if($errors->has())
				@foreach($errors->all() as $error)
					<div class="alert alert-danger">{{ $error }}</div>
				@endforeach
			@endif
			@if(Session::has('message'))
			<div class="alert alert-success">
				Sign up success!!!<br>Please Login to see your dashboard.
			</div>
			@endif
			<div style="padding-top:30px" class="panel-body">
				{{ Form::open(array('id' => 'full_loginform', 'class' => 'form-horizontal', 'url' => 'login')) }}
					<div class="input-group">
						<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
						{{ Form::text('username', null, ['class' => 'form-control', 'placeholder' => 'Username']) }}
					</div>
					<div style="margin-top: 25px" class="input-group">
						<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
						{{ Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password']) }}
					</div>

					<div style="margin-top:25px" class="form-group">
						<!-- Button -->
						<div class="col-sm-12 controls">
							{{ Form::button("Login", array("class"=>"btn btn-success",'type' => 'submit')) }}
							<!--<a id="btn-fblogin" href="#" class="btn btn-primary">Login with Facebook</a>-->
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-12 control">
							<div style="border-top: 1px solid#888; padding-top:15px; font-size:85%" >
								Forgot your password?
							<a href="login/password/remind">
								Click Here!
							</a>
							<span class="pull-right">Doesn't have an account? <a href="{{ url('login/register') }}">Register here!</a></span>
							</div>
						</div>
					</div>
				{{ Form::close() }}
			</div>
		</div>
	</div>
@stop