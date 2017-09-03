@extends('layouts.app')
@section('title')
	QuickBooks Online Setup
@endsection
@section('style')
<style>
    .spin {
      -webkit-animation: spin 1.15s infinite linear;
      -moz-animation: spin 1.15s infinite linear;
      -o-animation: spin 1.15s infinite linear;
      animation: spin 1.15s infinite linear;
         -webkit-transform-origin: 50% 50%;
             transform-origin:50% 50%;
             -ms-transform-origin:50% 50%; /* IE 9 */
    }

    @-moz-keyframes spin {
      from {
        -moz-transform: rotate(0deg);
      }
      to {
        -moz-transform: rotate(360deg);
      }
    }

    @-webkit-keyframes spin {
      from {
        -webkit-transform: rotate(0deg);
      }
      to {
        -webkit-transform: rotate(360deg);
      }
    }

    @keyframes spin {
      from {
        transform: rotate(0deg);
      }
      to {
        transform: rotate(360deg);
      }
    }
<</style>
@endsection
@section('content_header')
    QuickBooks Online Setup
@endsection
@section('content')
    <div class="row">
    	<div class="col-md-8 col-md-offset-2">
    		<div class="panel panel-default panel-h-offset">
                <div class="panel-heading">
                    First Time Sync.
                </div>
    			<div id="panel-body" class="panel-body panel-body-overflow">
                    <div class="flex-center">
                        <p>SnapDsk is syncing your QuickBooks Online data...this may take a while if there is a lot of data.</p>
                    </div>
                    <br>
    				
                    <div class="flex-center">
                        <span>
                            <img src="{{ url('/qbo/media/QuickBooks_Logo_Horz.png') }}" style="width:200px;" />                            
                        </span>
                        <span style="margin:0px 20px 0px 20px;">
                            <i id="sync_spinner" class="fa fa-refresh spin" aria-hidden="true" style="font-size: 60px; color: deepskyblue;"></i>
                        </span>
                        <span style="margin-top:5px;">
                            <img src="/file/image/snapdsk_logo_1100x300.png" width="200"/>
                        </span>
                    </div>
                    <br>
                </div>						
    		</div>
    	</div>
    </div>
@endsection
@section('footer_content')
@endsection
@section('popups')
<!-- Popup -->
<div id="popup_setup_complete" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style="font-size:30px; width:32px; height:32px;">&times;</button>
                <h4 class="modal-title">Setup Complete</h4>
            </div>
            <div class="modal-body">
                <p>QuickBooks Online setup is complete.  Click 'OK' to begin using SnapDsk!</p>
            </div>            
            <div class="modal-footer">                
                <button type="button" class="btn btn-primary" onclick="button_ok()" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    var result = $.ajax({
        url: "/users/setup_sync",
        type:"GET",
        success: function(data) 
        {
            if(data == 1){
                // $( "#progressbar" ).progressbar({
                //   value: 100
                // });
                $("#sync_spinner").attr('class','fa fa-refresh');
                $("#popup_setup_complete").modal();                    
            }
        },
        error: function(e) {
            console.log(e);
        }
    });

    function button_ok(){
        window.location.href = '/customers';
    }
</script>
@endpush





