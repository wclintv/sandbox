@extends('layouts.app')
@section('title')
    Settings 
@endsection
@section('content_header')
    Settings
@endsection
@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    QuickBooks Online Settings
                </div>
                <div class="panel-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <div class="col-sm-1">
                                <label class="control-label">Status: </label>
                            </div>                   

                            @if(session('quickbooks_connection') == 1)
                                <div class="col-sm-2">
                                    <label class="col-sm-2" style="color: green;">Connected</label>
                                </div>
                                <div class="col-sm-3">
                                    <a class="btn btn-primary" style="width:180px; height:30px;" href="/quickbooks/disconnect">Disconnect</a>
                                </div>                            
                                
                                <div class="col-sm-4">
                                    <p>You are connected to QuickBooks Online and everything is running normally.</p> 
                                </div>
                            @else
                                <div class="col-sm-2">
                                    <label class="col-sm-2 control-label" style="color: crimson; text-align: left; font-weight: normal;">Disconnected</label>
                                </div>
                                <div class="col-sm-3">
                                    <a style="cursor: pointer;">
                                        <img src="/quickbooks/images/C2QB_default" onclick="intuit.ipp.anywhere.controller.onConnectToIntuitClicked();" style="width:200px;"/>
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
@section('footer_content')
    <div class="flex-center footer-content">
        <span class="footer-span">  
            <button type="button" class="btn btn-primary" onclick="save_popup()" data-dismiss="modal">Save</button>           
        </span>
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



        function quickbooks_disconnect(){
            $.get("/quickbooks/disconnect", function(data, status){
                if(status <= 299){
                    console.log(data);
                }
                
            });
        }
        function quickbooks_reconnect(){

        }
    </script>
@endpush