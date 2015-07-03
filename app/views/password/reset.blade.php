@extends('master')

@section('content')
	<div class="col-md-6">
		<div class="panel panel-info">
			<div class="panel-heading">
				Set up new password
			</div>
			@if(Session::has('error'))
				<div class="alert alert-danger">
					{{ Session::get('error') }}
				</div>
			@endif
			<div class="panel-body">
				<form action="{{ action('RemindersController@postReset') }}" method="POST">
					<input type="hidden" name="token" value="{{ $token }}">
					<div class="row form-group">
						<label for="email" class="col-md-3 control-label">Your email:</label>
						<input type="email" name="email">
					</div>
					<div class="row form-group">
						<label for="password" class="col-md-3 control-label">New Password:</label>
						<input type="password" name="password">
					</div>
					<div class="row form-group">
						<label for="email" class="col-md-3 control-label">Confirm Password:</label>
						<input type="password" name="password_confirmation">
					</div>
					<input type="submit" value="Reset Password">
				</form>
			</div>
		</div>
	</div>
@stop