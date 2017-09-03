<div id="page_popup" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style="font-size:30px; width:32px; height:32px;">&times;</button>
                <h4 class="modal-title">Setup Complete</h4>
            </div>
            <div class="modal-body">
                <p>@yield('popup_message')</p>
            </div>            
            <div class="modal-footer">                
                <button type="button" class="btn btn-primary" onclick="button_ok()" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
<input id="popup_destination" type="hidden" value="@yield('popup_destination')">
@push('scripts')
<script>
    
    function button_ok(){
        var destination = $('#popup_destination').val();
        window.location.href = destination;
    }
</script>

@endpush