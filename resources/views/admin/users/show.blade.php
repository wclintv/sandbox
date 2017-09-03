@extends('layouts.app')
@section('title')
	Profile
@endsection
@section('content_header')
    profile
@endsection
@section('content')
    <div class="row">
    	<div class="col-md-8 col-md-offset-2">
    		<div class="panel panel-default panel-h-offset">
                <div class="panel-heading">
                    <h4>{{ Auth::user()->name }}</h4>
                </div>
    			<div id="panel-body" class="panel-body panel-body-overflow">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <div class="col-sm-2">
                                <label class="control-label">First Name:</label>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label">{{ Auth::user()->firstname }}</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2">
                                <label class="control-label">Last Name:</label>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label">{{ Auth::user()->lastname }}</label>
                            </div>          

                        </div>
                        <div class="form-group">
                            <div class="col-sm-2">
                                <label class="control-label">Email:</label>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label">{{ Auth::user()->email }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="flex-center">
                        <p>To make changes to your user information in SnapDsk, please update your Intuit account user profile, the changes will be updated in SnapDsk the next time you login.</p>
                    </div>
                    <br>

                        <a style="cursor:pointer;" onclick="user_delete()">Cancel Account</a>   

                    
    			</div>
                					
    		</div>
    	</div>
    </div>
@endsection
@section('footer_content')
@endsection
@push('scripts')    
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
        function user_delete()    
        {
            confirm('Are you sure you want to cancel your SnapDsk account?');
            intuit.ipp.anywhere.logout(function(){window.location.href = "/users/delete";});                      
        }
    </script>
@endpush





