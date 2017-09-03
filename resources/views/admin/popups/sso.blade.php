<div id="sso_popup" class="modal fade" role="dialog">
    <div class="modal-dialog" role="document">
        <div  class="modal-content">
            <div id="sso_popup_body" class="modal-body">
<!--                 <p>Testing sso popup</p>
                <iframe id="frame" src=""></iframe> -->
            </div>
            
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
     $("#sso_popup").modal();



        //$("#sso_popup_body").load("{{ $authUrl }}");
    });


        $("#sso_popup").on('show.bs.modal', function(event){
            console.log('show.bs.modal fired!');
            var modal = $(this);
            modal.find('.modal-body').load("https://www.google.com");

        });
   
</script>
