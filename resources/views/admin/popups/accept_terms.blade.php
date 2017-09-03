<div id="page_popup" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="cancel_popup()" data-dismiss="modal" style="font-size:30px; width:32px; height:32px;">&times;</button>
                <h4 class="modal-title">Terms of Service, Privacy Policy,  Use Agreement</h4>
            </div>
            <div class="modal-body">
                <textarea readonly="true" style="height:200px; width:100%;">{{ Application::terms_of_service() }}
                    &#013;&#010;*******************************************************************************
                    {{ Application::privacy_policy() }}
                    &#013;&#010;*******************************************************************************
                    {{ Application::use_agreement() }}</textarea>
            </div>
          <!--   <hr style="margin-bottom: 0px;">
            <div class="modal-header">
                <h4 class="modal-title">Privacy Policy</h4>
            </div>
            <div class="modal-body">
                <textarea readonly="true" style="height:200px; width:100%;">{{ Application::privacy_policy() }}</textarea>
            </div>
            <hr style="margin-bottom: 0px;">
            <div class="modal-header">
                <h4 class="modal-title">Use Agreement</h4>
            </div>
            <div class="modal-body">
                <textarea readonly="true" style="height:200px; width:100%;">{{ Application::use_agreement() }}</textarea>
            </div> -->
            <div class="modal-footer">
                <span style="float:left; text-align: left;">By clicking the 'Accept' button you agree to the snapdsk.com™ Terms of Service Agreement, Privacy Policy, and Use Agreement.</span>
                <button type="button" class="btn btn-primary" onclick="accept_terms()" data-dismiss="modal">Accept</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $("#page_popup").modal();
    function accept_terms(){
        var now = new Date();
         $.ajax({
            type: "POST",
            url: "/users/accept_terms",
            dataType: "json",
            data: {
                terms_accepted : true
            },
            success: function(data) {
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