<style>
    .menu-icon{
        width: 28px;
        line-height: 36px;
        font-size: 18px;
    }
</style>
<nav class="navbar navbar-default navbar-fixed-top" > 
    <div class="container">  
        <div class="navbar-header">
            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>  
            <!-- Branding Image -->
            @if(config('app.debug') == true)
                <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name') . ' v' . config('app.version') }}</a>
            @else
                <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name') }}</a>
            @endif
        @if(Auth::check())  
            <ul id="ul_plus" class="nav no-padding" style="position: fixed; height: 100%; width:60px;text-align: center;">
                <li class="dropdown">
                    <a id="plus_link" href="#" data-toggle="dropdown" role="button" aria-expanded="true"></a>              
                    <ul class="dropdown-menu no-padding" style="transform: translate(-34%, 0);" role="menu">
                        <li style="border:1px solid transparent;" >     
                            <a href="/customers/create"><i class="fa fa-plus menu-icon" aria-hidden="true"></i>Create Customer</a>                   
                        </li>
                    </ul>                    
                </li>
            </ul> 
        @endif
        </div>
        <div class="collapse navbar-collapse" id="app-navbar-collapse">  
            <!-- Right Side Of Navbar -->
            @if(Auth::check())
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a id="menu_link" href="#" data-toggle="dropdown" role="button" aria-expanded="true"></a>              
                    <ul class="dropdown-menu" role="menu">
                        <li>
                            
                                <a href="/customers"><i class="fa fa-users menu-icon" aria-hidden="true"></i>Customers</a>
                                <a href="/quickbooks" target="_blank"><img src="/qbo/media/QuickBooks_Logo_Horz.png" width="140" style="margin-left: -10px;" /></a>
                                @if(config('app.debug') == true)
                                    <hr>
                                    <a href="/apiexplorer"><i class="fa fa-database menu-icon" aria-hidden="true"></i>Api Explorer</a>
                                    <a href="/requirements"><i class="fa fa-check-square-o menu-icon" aria-hidden="true"></i>Requirements</a>
                                @endif
                                <hr>  
                                                         
                            <a href="/about"><i class="fa fa-info menu-icon" style="margin-left: 5px;" aria-hidden="true"></i>About</a>
                            <a href="/contact"><i class="fa fa-envelope-o menu-icon" aria-hidden="true"></i>Contact</a>
                            <a href="/support"><i class="fa fa-wrench menu-icon" aria-hidden="true"></i>Support</a>
                            <hr>
                            <a href="/terms_of_service"><i class="fa fa-balance-scale menu-icon" aria-hidden="true"></i>Service Agreement</a>
                            <a href="/privacy_policy"><i class="fa fa-balance-scale menu-icon" aria-hidden="true"></i>Privacy Policy</a>
                            <a href="/use_agreement"><i class="fa fa-balance-scale menu-icon" aria-hidden="true"></i>Use Agreement</a>
                        </li>
                    </ul>
                </li>
            </ul> 
            @endif 
            @if(Auth::user() != null)
                @if(Auth::user()->qb_is_admin)
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a id="settings_link" href="#" data-toggle="dropdown" role="button" aria-expanded="true"></a>              
                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="/users"><i class="fa fa-id-card-o menu-icon" aria-hidden="true"></i>Users</a>
                                    <a href="/settings"><i class="fa fa-cog menu-icon" aria-hidden="true"></i>Settings</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                @endif
            @endif           
            <ul class="nav navbar-nav navbar-right">
                <!-- Authentication Links -->
                @if (Auth::guest())
                   <!--  <li><a href="{{ route('login') }}">Login</a></li> -->
                    <li>
                        <a href="/sso/intuit/login?openid_callback=true">
                            <img src="/qbo/media/Sign_in_blue_btn_med_default.png" 
                                 onmouseout="this.src='/qbo/media/Sign_in_blue_btn_med_default.png'"
                                 onmouseover="this.src='/qbo/media/Sign_in_blue_btn_med_hover.png'"
                                 style="width:120px; margin: -5px 0px -5px 0px;"/>
                        </a>
                    </li>
                    <!--<li><a href="{{ route('register') }}">Register</a></li>-->
                @else
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            <div class="header-menu-text">{{ Auth::user()->name }}<span class="caret"></span> </div>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li>
                                <a href="/users/profile"><i class="fa fa-user menu-icon" aria-hidden="true"></i>Profile</a>
                                <a href="javascript:void(0)" onclick="logout()"><i class="fa fa-sign-out menu-icon" aria-hidden="true"></i>Sign Out</a>
                                <!-- <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a> -->
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>       
</nav> 
@push('scripts')
    <script>
        $(document).ready(function(){
            menu_icon_set();
            $(window).resize(function(){
                menu_icon_set();
            });
        });
        function logout(){
            intuit.ipp.anywhere.logout(function(){document.getElementById('logout-form').submit();});            
        }
        function menu_icon_set(){
            $('#menu_link').empty();
            var w = window.innerWidth;
            var menu_icon = '<i class="fa fa-bars no-padding fa-2x" aria-hidden="true"></i>';
            var menu_text = '<div class="header-menu-text">Menu<span class="caret"></span></div>';
            var plus_icon = '<i class="fa fa-plus" style="font-size:28px; line-height:38px; color:grey;" aria-hidden="true"></i>';
            var plus_text = '<i class="fa fa-plus" style="font-size:22px; line-height:30px; color:grey;" aria-hidden="true"></i>';
            var settings_icon = '<i class="fa fa-cog fa-2x" aria-hidden="true"></i>';
            var settings_text = '<div class="header-menu-text">Settings<span class="caret"></span></div>'
            if(w > 760)
            {
                $('#menu_link').html(menu_icon);
                $('#settings_link').html(settings_icon);
                $('#plus_link').html(plus_icon);
                $('#ul_plus').css('left','calc((100% - 60px) / 2)');
            }
            else
            {
                $('#menu_link').html(menu_text);
                $('#settings_link').html(settings_text);
                $("#plus_link").html(plus_text);
                $('#ul_plus').css('left','calc((100% - 60px) / 2)');
            }    
        }
    </script>
@endpush      
