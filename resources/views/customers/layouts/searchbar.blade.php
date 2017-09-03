<div class="container-fluid no-padding">
    <div class="row flex-center no-padding">
        <div class="input-group row-min-width" style="margin:15px 0px 15px 0px; padding-bottom: width:96%;min-width: 332px; max-width: 600px;">
            <input id="search_text" class="form-control"  type="text" placeholder="search..."/>
            <span class="input-group-btn">
                <button id="btnDatatable" class="btn btn-group" style="border-top: 1px solid silver; border-bottom: 1px solid silver" onclick="btnDatatable_Click()">
                    <i class="fa fa-list-alt" aria-hidden="true"></i>
                </button>
                <button id="btnSearch" class="btn btn-group btn-primary" onclick="btnSearch_Click()">
                    <i class="fa fa-search"></i>
                </button>            
            </span>
        </div>
    </div>
</div>
@push('scripts')
    <style>
    .search-list{
        width:16.6%;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        display: inline-block;
    }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            $("#search_text").autocomplete({
                autoFocus: true,
                source: function(request, response) {
                    $.ajax({
                        url: "/customers/search",
                        dataType: "json",
                        data: {
                            term : request.term
                        },
                        success: function(data) {
                            if(data.length == 0){
                                var obj = {lastname:"No Results Found."};
                                data.push(obj);
                                //console.log(data);
                            }
                            
                            response(data);                           
                        },
                        error: function(jqXHR, exception){
                            if(jqXHR.status === 401){
                                window.location.href = "/login";
                            }
                        }
                    });
                },
                select: function(e, ui){
                    if(ui.item.lastname != "No Results Found."){
                        window.location.href = "/customers/" + ui.item.cust_id;
                    }                    
                },
                create: function(){
                    $(this).data('ui-autocomplete')._renderItem = function (ul, item){
                        return $( "<li>" )
                            .append( '<div style="width:100%;"><span class="search-list">' + sanitize(item.lastname) +  '</span><span class="search-list">' + sanitize(item.firstname) + '</span><span class="search-list" style="width:33.3%;">'+ sanitize(item.address1) +'</span><span class="search-list">'+ sanitize(item.phone) +'</span><span class="search-list">'+ sanitize(item.mobilephone) +'</span></div>' )
                            .appendTo( ul );
                    }; 
                },
                min_length: 3,
            }); 
        });
        function sanitize(input){
            if(input != null){
                var output = input.replace(/<script[^>]*?>.*?<\/script>/gi, '').
                    replace(/<[\/\!]*?[^<>]*?>/gi, '').
                    replace(/<style[^>]*?>.*?<\/style>/gi, '').
                    replace(/<![\s\S]*?--[ \t\n\r]*>/gi, '');
                return output;                
            }
            return '';

        }
        function btnSearch_Click()
        {
            var txt = $('#search_text').val();
            search();
        }
        function btnDatatable_Click()
        {
            window.location.href = "/customers/datatable";
        }
        function search()
        {
            $("#search_text").autocomplete("search");
        }
    </script>
@endpush
