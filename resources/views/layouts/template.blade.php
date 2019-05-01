<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title>DDC EOC Dashboard</title>
	@include('layouts.incRequiredStylesheet')
	@yield('pageStyle')
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
	@include('layouts.incNavbar')
	@include('layouts.incMainSidebarContainer')
	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
		@yield('content')
	</div>
	<!-- /.content-wrapper -->
	@include('layouts.incControlSidebar')
	@include('layouts.incMainFooter')
</div>
<!-- ./wrapper -->
<!-- REQUIRED SCRIPTS -->
@include('layouts.incRequiredScript')
@yield('pageScript')
</body>
</html>
