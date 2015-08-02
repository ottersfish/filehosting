@extends('master')

@section('content')
    @if(Session::has('messages'))
    <div class="row">
        <div class="col-md-6 col-sm-8">
            <p class="alert alert-success">
                {{ @Session::get('messages') }}
            </p>
        </div>
    </div>
    @endif
    <div class="row">
        <div id="updateprofilebox" style="margin-top:10px;" class="mainbox col-md-6 col-sm-8">
            <div class="panel panel-info" >
                <div class="panel-heading">
                    <p class="panel-title">
                        Edit Profile
                    </p>
                </div>
                @if($errors->has())
                    @foreach($errors->all() as $error)
                        <div class="alert alert-danger">{{ $error }}</div>
                    @endforeach
                @endif
                <div style="padding-top:30px" class="panel-body">
                    {{ Form::model($user, array('method' => $method, 'url' => route('users.update'))) }}
                        <div class="row form-group">
                            <label for="name" class="col-md-3 control-label">Name:</label>
                            <div class="col-md-4">
                                {{ Form::text('name') }}
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="password" class="col-md-3 control-label">Password:</label>
                            <div class="col-md-4">
                                {{ Form::password('password') }}
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="con_password" class="col-md-3 control-label">Password confirmation:</label>
                            <div class="col-md-4">
                                {{ Form::password('con_password') }}
                            </div>
                        </div>
                        {{ Form::button("Update", array('class' => 'btn btn-success btn-sm', 'type' => 'submit')) }}
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop