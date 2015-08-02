@extends('master')

@section('content')
    @if(Session::has('error'))
        <div class="alert alert-danger">
            {{ Session::get('error') }}
        </div>
    @endif
    @if(Session::has('status'))
        <div class="row">
            <div class="col-md-6">
                <div class="alert alert-success">
                    {{ Session::get('status') }}
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <p class="panel-title">Reset Password</p>
                </div>
                <div class="panel-body">
                    <form action="{{ action('RemindersController@postRemind') }}" method="POST">
                        <div class="row form-group">
                            <label for="email" class="col-md-3 control-label">Your email:</label>
                            <input type="email" name="email">
                        </div>
                        <p class="help-block">
                            Please insert your email that was used for registration.
                            <br>We will send a link for resetting your password.
                        </p>
                        <button class="btn btn-warning btn-sm">
                            Reset Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop