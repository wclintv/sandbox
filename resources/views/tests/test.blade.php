<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="google-site-verification" content="sZTM-DMZGRExOR7vXVMVkEPbWeFSkKwnbWRii76O77I" />

	<!-- CSRF Token -->
	<meta id="token" name="token" value="{{ csrf_token() }}">

	<!-- Styles -->    
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.15/css/jquery.dataTables.min.css" integrity="sha256-TntJ2hIwyiYc8GIhWzIt+PvYBfQE4VfxJnn+ea5kcJs=" crossorigin="anonymous" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" integrity="sha256-rByPlHULObEjJ6XQxW/flG2r+22R5dKiAoef+aXWfik=" crossorigin="anonymous" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.min.css" integrity="sha256-MFTTStFZmJT7CqZBPyRVaJtI2P9ovNBbwmr0/KErfEc=" crossorigin="anonymous" />
	<link rel="stylesheet" href="{{ asset('css/app.css') }}" >

	<!-- Scripts -->
	<script type="text/javascript" src="https://appcenter.intuit.com/Content/IA/intuit.ipp.anywhere.js"></script>
	<script>window.Laravel = {!! json_encode(['csrfToken' => csrf_token(),]) !!};</script>
	<script src="{{ asset('js/app.js') }}"></script>  
	<!-- If the app is in debug mode, load the full scripts, else, load the 'min' version for production -->
	@if(config('app.debug') == true)
	<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.15/js/jquery.dataTables.js" integrity="sha256-E+/kL+CHUqkr0DmPPZ7ps65UqND+U/ZGCke2LM/XCLs=" crossorigin="anonymous"></script>    
	<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.3.4/vue.js" integrity="sha256-sawP1sLkcaA4YQJQWAtjahamgG6brGmaIJWRhYwDfno=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/vue-resource/1.3.4/vue-resource.js" integrity="sha256-8+8bZrGzTM4WqoYDOiYM8g19vF3l/R2zIs27nia7u5M=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
	@else
	<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.15/js/jquery.dataTables.min.js" integrity="sha256-j007R7R6ijEWPa1df7FeJ6AFbQeww0xgif2SJWZOhHw=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.2.6/vue.min.js" integrity="sha256-cWZZjnj99rynB+b8FaNGUivxc1kJSRa8ZM/E77cDq0I=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/vue-resource/1.3.1/vue-resource.min.js" integrity="sha256-vLNsWeWD+1TzgeVJX92ft87XtRoH3UVqKwbfB2nopMY=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" integrity="sha256-KM512VNnjElC30ehFwehXjx1YCHPiQkOPmqnrWtpccM=" crossorigin="anonymous"></script>
	@endif
	<script>
		$(document).ready(function(){
			menu_icon_set();
			$(window).resize(function(){
				menu_icon_set();
			});
		});
		function logout(){
			intuit.ipp.anywhere.logout();
			document.getElementById('logout-form').submit();
		}
		function menu_icon_set(){
			$('#menu_link').empty();
			var w = window.innerWidth;
			var menu_icon = '<i class="fa fa-bars fa-2x" aria-hidden="true"></i>';
			var menu_text = '<i class="fa fa-bars" style="font-size:28px; line-height:30px; color:grey; width:60px;" aria-hidden="true"></i>';
			var plus_icon = '<i class="fa fa-plus" style="font-size:28px; line-height:38px; color:grey; " aria-hidden="true"></i>';
			var plus_text = '<div class="header-menu-text" style="font-size:22px; line-height:30px;"><i class="fa fa-plus" aria-hidden="true"></i></div>';
			var settings_icon = '<i class="fa fa-cog fa-2x" aria-hidden="true"></i>';
			var settings_text = '<div class="header-menu-text">Settings<span class="caret"></span></div>'
			if(w > 760)
			{
				$('#header_container').attr('style', '""');
				
				$('#menu_link').html(menu_icon);
				$('#settings_link').html(settings_icon);
				$('#plus_link').html(plus_icon);

			}
			else
			{
				$('#header_container').attr('style', 'width:100%; margin:0 auto; padding:0;');
				$('#menu_link').html(menu_text);
				$("#menu_link").attr('width',10);
				$('#settings_link').html(settings_text);
				$("#plus_link").html(plus_text);
			}    
		}
	</script>
	<style>

	</style>    
</head>
<body>
	<div id="header_container" class="container" style="">

		<nav class="navbar navbar-default" role="navigation">
			

			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>

				<ul class="nav navbar-nav navbar-left" style="float:left; display: flex; padding:0px; margin:0 auto;">
					<li class="dropdown">
						<a id="menu_link" class="dropdown-toggle" width="56" href="#" data-toggle="dropdown" role="button" aria-expanded="true"></a>              
						<ul class="dropdown-menu" role="menu">
							<li>
								<a href="/customers">									
									<i class="fa fa-users" style="width: 25px;" aria-hidden="true">	</i>	
									Customers				
								</a>
								<a href="/quickbooks" target="_blank"><img src="/quickbooks/images/logo" width="120" style="margin-left: -6px;" /></a>
								<hr>
								<a href="/apiexplorer">Api Explorer</a>
								<a href="/requirements">Requirements</a>
								<hr>  
								<a href="/about">About</a>
								<a href="/contact">Contact</a>
								<a href="/support">Support</a>
								<hr>
								<a href="/terms_of_service">Service Agreement</a>
								<a href="/privacy_policy">Privacy Policy</a>
								<a href="/use_agreement">Use Agreement</a>                            
							</li>
						</ul>
					</li>

				</ul>
				

			</div>

			<!--   <a class="navbar-brand" style="border: 1px solid red;" href="#">Brand</a> -->
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-left">

				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li>
						<a href="/quickbooks/login?openid_callback=true">
							<img src="/quickbooks/images/intuit_signin_med_default" 
							onmouseout="this.src='/quickbooks/images/intuit_signin_med_default'"
							onmouseover="this.src='/quickbooks/images/intuit_signin_med_hover'"
							style="width:120px; margin: -5px 0px -5px 0px;"/>
						</a>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
							<div class="header-menu-text">{{ Auth::user()->name }}<span class="caret"></span> </div>
						</a>
						<ul class="dropdown-menu" role="menu">
							<li>
								<a href="/users/profile">Profile</a>
								<a href="javascript:void(0)" onclick="logout()">Logout</a>
								<!-- <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a> -->
								<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
									{{ csrf_field() }}
								</form>
							</li>
						</ul>
					</li>
					<li class="dropdown">
						<a id="settings_link" href="#" data-toggle="dropdown" role="button" aria-expanded="true"></a>              
						<ul class="dropdown-menu" role="menu">
							<li>
								<a href="/users">Users</a>
								<a href="/settings">Settings</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
			<a style="position:absolute; top:0; left:56px; min-width: 120px; font-size: 18px; color:grey; text-decoration: none; background-color: white; line-height: 51px;" href="{{ url('/') }}"><div style="margin-left:2px;">{{ config('app.name', 'snapdsk') }}</div></a>
		</nav>

	</div>
	
	









