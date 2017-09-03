<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="google-site-verification" content="sZTM-DMZGRExOR7vXVMVkEPbWeFSkKwnbWRii76O77I" />

    <!-- CSRF Token -->
    <meta id="token" name="token" value="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Styles -->    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.15/css/jquery.dataTables.min.css" integrity="sha256-TntJ2hIwyiYc8GIhWzIt+PvYBfQE4VfxJnn+ea5kcJs=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" integrity="sha256-rByPlHULObEjJ6XQxW/flG2r+22R5dKiAoef+aXWfik=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.min.css" integrity="sha256-MFTTStFZmJT7CqZBPyRVaJtI2P9ovNBbwmr0/KErfEc=" crossorigin="anonymous" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" >

    <!-- Scripts -->
    <script type="text/javascript" src="https://appcenter.intuit.com/Content/IA/intuit.ipp.anywhere.js"></script>
    <script>
        var url = '{{ QuickbooksOnline::grant_url() }}';
        //intuit anywhere setup            
        intuit.ipp.anywhere.setup({
            menuProxy: '',
            grantUrl: url,
            datasources: {
                quickbooks: true,
            },
        });
    </script>
    <script>window.Laravel = {!! json_encode(['csrfToken' => csrf_token(),]) !!};</script>
    <script src="{{ asset('js/app.js') }}"></script>  
	<script>
		(
			function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function()
			{
				(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			}
		)
		(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
		ga('create', 'UA-101501209-1', 'auto');
		ga('send', 'pageview');
	</script>
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
    <style>
        .loader {
            border: 16px solid #f3f3f3; /* Light grey */
            border-top: 16px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 80px;
            height: 80px;
            animation: spin_loader 2s linear infinite;
        }

        @keyframes spin_loader {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    @yield('style')
</head>
<body>
    <div id="app" style="overflow:hidden">
        @include('layouts.header')
        <div class="container affix" style="overflow-x:hidden;overflow-y:auto; width:100%; bottom:51px; top:51px;">
            <div class="flex-center">
                <div style="font-weight:300; font-size: 36px">
                    @yield('content_header')
                </div>
            </div> 
            @yield('content')            
        </div>        
        <div id="footer" class="nav navbar-default navbar-fixed-bottom" style="border-top:1px solid lightgrey; max-height: 50px;">
            @yield('footer_content') 
        </div>
        @yield('popups')   
    </div>
    @if(Auth::check())
        @if (Auth::user()->terms_accepted == null)
            @include('admin.popups.accept_terms');
        @endif
    @endif
    @yield("popups")
    <div id="popup_loader" class="modal fade" role="dialog">
        <div class="loader" style="position: relative; top: 40%; margin-left: calc((100% - 80px) / 2);"></div>
    </div>

    @stack('scripts')

            
      
</body>
</html>
