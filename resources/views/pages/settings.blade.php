@extends('layouts.app')
@section('title')
	Settings
@endsection
@section('content_header')
	settings
@endsection
@section('content')
 <div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default panel-h-offset">
            <div class="panel-heading">
                <h4>QuickBooks Online Settings</h4>
            </div>
            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-sm-1">
                            <label class="control-label">Status: </label>
                        </div>
                        @if(session('quickbooks_connection') == 1)
                            <div class="col-sm-2">
                                <label class="control-label" style="color: green; font-weight: normal;">Connected</label>
                            </div>
                            <div class="col-sm-3">
                                <a class="btn btn-primary" href="/quickbooks/disconnect" style="width: 180px;">Disconnect</a>
                            </div>                            
                            
                            <div class="col-sm-4">
                                <p>You are connected to QuickBooks Online and everything is running normally.</p> 
                            </div>
                        @else
                            <div class="col-sm-2">
                                <label class="control-label" style="color: crimson; text-align: left; font-weight: normal;">Disconnected</label>
                            </div>
                            <div class="col-sm-3">
                                <a style="cursor: pointer;">
                                    <img src="{{ url('/qbo/media/C2QB_green_btn_lg_default.png') }}" onclick="intuit.ipp.anywhere.controller.onConnectToIntuitClicked();" style="width:180px;"/>
                                </a>
                            </div>                    
                            <div class="col-sm-4">
                                <p>You are not currently connected to QuickBooks Online.  Click the <strong>'Connect to QuickBooks'</strong> button to continue using SnapDsk.</p> 
                            </div>                                
                        @endif                     
                    </div>                                 
                </div>  
            </div>
        </div>
    </div>
</div>
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
    </script>
@endpush

