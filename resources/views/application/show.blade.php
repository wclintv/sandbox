@extends('layouts.app')
@section('pagetitle')
    Application Settings 
@endsection
@section('content_header')
    Application Settings
@endsection
@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Terms of Service
                </div>
                <div class="panel-body">
                    <textarea id="terms_of_service" style="height:300px; width:100%;">{{ Application::terms_of_service() }}</textarea>   
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Privacy Policy
                </div>
                <div class="panel-body">
                    <textarea id="privacy_policy" style="height:300px; width:100%;">{{ Application::privacy_policy() }}</textarea>   
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Use Agreement
                </div>
                <div class="panel-body">
                    <textarea id="use_agreement" style="height:300px; width:100%;">{{ Application::use_agreement() }}</textarea>   
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
	function show_popup(){
		$("#page_popup").modal();
	}
	function save_popup(){
		$.ajax({
            type: "POST",
            url: "api/application",
            dataType: "json",
            data: {
                privacy_policy : $("#privacy_policy").val(),
                terms_of_service : $("#terms_of_service").val(),
                use_agreement : $("#use_agreement").val()
            },
            success: function(data) {
                alert("Application settings saved successfully.")
                //console.log(data);
                //window.location.href="/customers";          
            },
            error: function(jqXHR, exception){
                if(jqXHR.status === 401){
                    window.location.href = "/login";
                }
                console.log(exception);
            }
        });        
	}
</script>
@endpush