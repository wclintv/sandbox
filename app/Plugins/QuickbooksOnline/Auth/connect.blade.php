@extends('layouts.app')

@section('pagetitle')
	Connect to QuickBooks Online
@endsection
@section('content_header')
	Connect to QuickBooks Online
@endsection
@section('content')
<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4>You are not currently connected to QuickBooks Online...</h4>
			</div>
			<div class="panel-body">
				<div>
					<h4>OPTION 1:  QuickBooks Online Admin User.</h4>
					<p>If you are the Admin User of your QuickBooks Online company, click the connect button to begin using SnapDsk.</p>	
				</div>
                    <a style="cursor: pointer;">
                        <img src="{{ url('/qbo/media/C2QB_green_btn_lg_default.png') }}" onclick="intuit.ipp.anywhere.controller.onConnectToIntuitClicked();" style="width:260px;"/>
                    </a>	
				<br>
				<hr>
				<div>
					<h4>OPTION 2:  Non-Admin User.</h4>
					<p>Trying to join your company's SnapDsk app?</p>
					<p>The QuickBooks Online Admin User must email an invitation to you.  The Admin User can send invitations from the 'Users' page in the settings menu.</p>
				</div>
				<br>
			</div>
		</div>
	</div>
</div>
@endsection



@push('scripts')
<script>
    //intuit anywhere setup            
    intuit.ipp.anywhere.setup({
        menuProxy: '',
        grantUrl: "{{ QuickbooksOnline::grant_url() }}",
        datasources: {
            quickbooks: true,
        },
    });

    function btn_invite_click(){
    	window.location.href = "/users/profile";
    }
</script>

@endpush