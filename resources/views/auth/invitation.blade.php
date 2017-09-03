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

    <link rel="stylesheet" href="{{ asset('css/app.css') }}" >


</head>
<body>
    <div id="app" style="overflow:hidden">
        <div class="container affix" style="overflow-x:hidden;overflow-y:auto; width:100%; bottom:51px; top:51px;">
            <div class="flex-center">
                <div style="font-weight:300; font-size: 36px">
                    SnapDsk Invitation Found!
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-default">
                        <div class="panel-body">                    
                        <div class="flex-center">
                            <p>You're almost there! SnapDsk uses your QuickBooks Online account to sign in. Please sign in to Intuit to complete your registration.</p> 
                        </div>
                        <br>
                        <div class="flex-center">
                            <a href="/sso/intuit/invitation?i_token={{ $encrypted_token }}">
                                <img src="/qbo/media/Sign_in_blue_btn_lg_default.png" 
                                 onmouseout="this.src='/qbo/media/Sign_in_blue_btn_lg_default.png'"
                                 onmouseover="this.src='/qbo/media/Sign_in_blue_btn_lg_hover.png'"
                                 style="width:260px;"/>
                            </a>
                        </div>
                        <br>     
                        </div>
                    </div>
                </div>
            </div>          
        </div>        
   
        @yield('popups')     
    </div>
</body>
</html>