<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>FileHost - One Click File Hosting</title>

		<!-- Bootstrap -->
		<link href="{{asset('bootstrap/css/bootstrap.css')}}" rel="stylesheet">

		<!-- DataTables CSS -->
		<!-- <link href="{{ asset('bootstrap/css/plugins/dataTables.bootstrap.css') }}" rel="stylesheet"> -->
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<div class="header">
		<!-- Fixed navbar -->
			<nav class="navbar navbar-inverse navbar-static-top">
				<div class="container">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="{{url()}}">
							<img src="{{asset('bootstrap/img/logo.png')}}" height="40px" style="margin-top:-10px">
						</a>
					</div>
	 				<div id="navbar" class="navbar-collapse collapse">
	 					<ul class="nav navbar-nav">
	 						@if(Auth::check())
	 							<?php if(Auth::user()->is_admin)$prefix='admin/';else $prefix='home/';?>
								<li><a href="{{ url('/') }}"><span class="glyphicon glyphicon-home"></span> Home</a></li>
								@if(Auth::user()->is_admin)<li><a href="{{ url('admin/users') }}"><span class="glyphicon glyphicon-user"></span> Users</a></li>@endif
								<li><a href="{{ url($prefix.'files') }}"><span class="glyphicon glyphicon-file"></span> Files</a></li>
							@else
								<li><a href="{{url()}}"><span class="glyphicon glyphicon-arrow-up"></span> Upload</a></li>
							@endif
						</ul>
	 					<ul class="nav navbar-nav navbar-right">
	 						<li class="dropdown">
		 						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Welcome back, @if(!Auth::check()) {{'Guest'}}@else {{Auth::user()->username}}@endif! <span class="caret"></span></a>
		 						<ul class="dropdown-menu">
		 							@if(Auth::check())
										<li><a href="{{url('login/logout')}}">Logout </a></li>
									@else
										<li><a href="{{url('login/register')}}">Register </a></li>
										<li class="divider"></li>
										<li><a href="{{url('login')}}">Login </a></li>
									@endif
		 						</ul>
		 					</li>
	 					</ul>
	 				</div>
				</div>
			</nav>
    	<!-- /.fixed-navbar -->
    	</div>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					@yield('content')
				</div>
			</div>
		</div>
		<footer class="footer">
			<div class="push"></div>
	        <div class="container">
	            <div class="row">
	                <div class="col-lg-12">
	                    <p class="copyright text-muted small">Copyright &copy; 2015. All Rights Reserved</p>
	                </div>
	            </div>
        	</div>
        </footer>
	    <!-- /.footer -->

		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
		<!-- DataTables JavaScript -->
		<!-- // <script src="{{ asset('bootstrap/js/plugins/dataTables/jquery.dataTables.js') }}"></script> -->
		<!-- // <script src="{{ asset('bootstrap/js/plugins/dataTables/dataTables.bootstrap.js') }}"></script> -->

		@yield('scripts')
	</body>
</html>