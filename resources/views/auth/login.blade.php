@extends('layouts.app') 
@section('title')
    Login
@endsection
@section('content_header')
    login
@endsection  
@section('content')
<!-- <script type="text/javascript" src="https://appcenter.intuit.com/Content/IA/intuit.ipp.anywhere-1.3.3.js"></script>  -->
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="flex-center">
                        <p>SnapDsk uses your Intuit account to sign in.</p> 
                    </div>
                    <div class="flex-center">
                        <a href="/quickbooks/login?openid_callback=true">
                            <img src="/quickbooks/images/intuit_signin_lg_default" 
                                 onmouseout="this.src='/quickbooks/images/intuit_signin_lg_default'"
                                 onmouseover="this.src='/quickbooks/images/intuit_signin_lg_hover'"
                                 style="width:220px;"/>
                        </a>  

                    </div>
                              <br>             

<!--

                    <div class="links" align="center">
                        <a href="{{ URL::to('qk_login') }}">No thanks, i don't use quickbooks online...</a>
                    </div>
                    
                    <br><br>

                    <form class="form-horizontal" role="form" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>

                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    Forgot Your Password?
                                </a> 
                            </div>
                        </div>
                    </form>
                -->
            </div>
        </div>
    </div>
</div>
</div>
@endsection




