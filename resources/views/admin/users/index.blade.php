@extends('layouts.app')
@section('title')
	Users
@endsection
@section('content_header')
    users
@endsection
@section('style')
        <style>
        .no-bold{
            font-weight: normal;
        }
        .heading-button{
            position: absolute;
            height: 40px; 
            top: 14px;             
            right:14px; 
        }
        
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default panel-h-offset">
                <div class="panel-heading">
                    <h4>Active Users:</h4>
                </div>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <th class="no-bold">{{ $user->name }}</th>
                                    <th class="no-bold">{{ $user->email }}</th>
                                    <th class="no-bold">{{ $user->qb_is_admin ? 'yes' : 'no' }}</th>
                                    @if($user->qb_is_admin)
                                        <th></th>
                                    @else
                                        <th class="no-bold"><a class="btn btn-default" onclick="btn_user_delete_confirm({{ $user }})">Delete</a></th>
                                    @endif                                    
                                </tr>
                            @endforeach                            
                        </tbody>
                    </table>                 
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default panel-h-offset">
                <div id="panel-heading" class="panel-heading">
                    <h4>Invitations:</h4>
                    <button type="button" class="btn btn-primary heading-button"  data-toggle="modal" data-target="#popup_invite_new_user" data-backdrop="static">Invite a New User</button>                 
                </div>
                @if(count($invitations) > 0)                
                <div class="panel-body">

                    <table class="table">
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Expires</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invitations as $invitation)
                                <tr>
                                    <th class="no-bold">{{ $invitation->firstname }}</th>
                                    <th class="no-bold">{{ $invitation->lastname }}</th>
                                    <th class="no-bold">{{ $invitation->email }}</th>
                                    <th class="no-bold">{{ $invitation->expires() }}</th>
                                    <th class="no-bold"><a class="btn btn-default" onclick="btn_invitation_delete_confirm({{ $invitation }})">Cancel</a></th>
                                    <th class="no-bold"><a class="btn btn-default" onclick="btn_invitation_resend({{ $invitation }})">Resend</a></th>
                                </tr>
                            @endforeach                            
                        </tbody>
                    </table>                    
                </div>
                @endif 
            </div>
        </div>
    </div>
@endsection
@section('footer_content')
@endsection
@section('popups')
<!-- Popup -->
<div id="popup_invite_new_user" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style="font-size:30px; width:32px; height:32px;">&times;</button>
                <img src="{{ url('/application/icon') }}" style="width: 40px; height: 40px; margin-right: 10px; margin-top:-5px; float:left">
                <h4 class="modal-title">Invite a new user.</h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">First Name:</label>
                        <span class="col-sm-6">                      
                            <input id="txt_firstname" type="text" class="form-control" onchange="invitation_validate()" />
                        </span>
                        <span class="col-sm-3">
                            <label id="lbl_error_firstname" class="text-danger control-label"></label>
                        </span>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Last Name:</label>
                        <span = class="col-sm-6">
                            <input id="txt_lastname" type="text" class="form-control" onchange="invitation_validate()"/>
                        </span>
                        <span class="col-sm-3">
                            <label id="lbl_error_lastname" class="text-danger control-label"></label>
                        </span>
                    </div>  
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Email:</label>
                        <span = class="col-sm-6">
                            <input id="txt_email" type="text" class="form-control" onchange="invitation_validate()" onfocusout="invitation_validate()"/>
                        </span>
                        <span class="col-sm-3">
                            <label id="lbl_error_email" class="text-danger control-label"></label>
                        </span>
                    </div>
                </div>
            </div>            
            <div class="modal-footer">         
                <button type="button" class="btn btn-default" style="width: 120px;" onclick="invitation_reset()" data-dismiss="modal">Cancel</button>   
                <button id="btn_invite_send" type="button" class="btn btn-primary" disabled="true" style="width: 120px;" onclick="btn_invite_send()">Invite</button>                
            </div>
        </div>
    </div>
</div>
<div id="popup_invite_success" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style="font-size:30px; width:32px; height:32px;">&times;</button>
                <h4 class="modal-title"><img src="{{ url('/application/icon') }}" style="width: 40px; height: 40px; margin-right: 10px;">Invitation Sent!</h4>
            </div>
            <div class="modal-body">
                <p>
                    An invitation has been sent to <label id="lbl_invitation_email"></label>.  The recipient needs to click the included link to complete their registation.  Be sure to check the 'Junk' email folder if the invitation is not found in the 'Inbox'.                             
                </p>
                <p>Please allow 5 mins for the email to arrive. The invitation expires in 48 hours.</p>
            </div>            
            <div class="modal-footer">         
                <button type="button" class="btn btn-default" onclick="btn_ok_click()" style="width: 120px;" data-dismiss="modal">OK</button>                
            </div>
        </div>
    </div>
</div>
<div id="popup_message" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style="font-size:30px; width:32px; height:32px;">&times;</button>
                <img src="{{ url('/application/icon') }}" style="width: 40px; height: 40px; margin-right: 10px; margin-top:-5px; float:left">
                <h4 id="popup_message_header" class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <p id="popup_message_body"></p>
            </div>            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" style="width: 120px;" data-dismiss="modal">OK</button>                  
            </div>
        </div>
    </div>
</div>
<div id="popup_confirm" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style="font-size:30px; width:32px; height:32px;">&times;</button>
                <h4 id="popup_confirm_header" class="modal-title">
                    <img src="{{ url('/application/icon') }}" style="width: 40px; height: 40px; margin-right: 10px;">
                </h4>
            </div>
            <div class="modal-body">
                <p id="popup_confirm_body"></p>
            </div>            
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" style="width: 120px;" data-dismiss="modal">No</button>         
                <button id="popup_yes_btn" type="button" class="btn btn-default" onclick="btn_delete()" style="width: 120px;" data-dismiss="modal">Yes</button>                
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')    
    <script>
        var selected_invitation;
        var selected_user;
        var error;

        //Event Handlers
        $("#popup_invite_new_user").on("shown.bs.modal", function(){
            $("#txt_firstname").focus();
        });

        //Methods
        function btn_invite_send(){
            var invitation = {
                "firstname": $("#txt_firstname").val(),
                "lastname": $("#txt_lastname").val(),
                "email": $("#txt_email").val(),
            } 
            $.ajax({
                type: "POST",
                url: "/api/invitation/send",
                data: invitation,
                success: function(data)
                {
                    console.log(data);
                    $("#popup_invite_new_user").modal('toggle');
                    invitation_reset();
                    $("#lbl_invitation_email").html(data);
                    $("#popup_invite_success").attr('data-backdrop','static');
                    //$("#popup_invite_success").attr('data-keyboard','false');
                    $("#popup_invite_success").modal();
                                        
                },
                error: function(e)
                {
                    $("#popup_invite_new_user").modal('toggle');
                    invitation_reset();
                    console.log(e);
                    console.log("Response Text:  " + e.responseText);
                    if(e.responseText == "That user is already setup.")
                    {                        
                        $("#popup_message").modal();
                        $("#popup_message_header").html('Invitation not sent.');
                        $("#popup_message_body").html('The invitation was not sent.  The user with that email has already been assigned to a QuickBooks Online company.')
                    }
                    if(e.responseText == "That invitation has already been sent.")
                    {
                        alert('That invitation has already been sent.  If you wish to resend it, use the "Resend" link next to the open invitation.');
                    }
                }
            });
        }
        function btn_invitation_resend(invitation)
        {
            $.ajax({
                type: "POST",
                url: "/api/invitation/resend",
                data: invitation,
                success: function(data)
                {
                    console.log(data);
                    $("#lbl_invitation_email").html(data);
                    $("#popup_invite_success").modal();
                },
                error: function(e)
                {
                    console.log(e);
                    console.log("Response Text:  " + e.responseText);
                    if(e.responseText == "That user already setup.")
                    {
                        alert('The invitation was not sent.  The user with that email is already setup, so there is no need to send another invitation.');
                    }
                }
            });
        }
        function btn_delete()
        {
            var _select = $("#popup_confirm").attr("data-object");
            var _url;
            var _data;

            if(_select == "invitation")
            {
                _url = "/api/invitation/delete";
                _data = this.selected_invitation;
            }
            if(_select == "user")
            {
                _url = "/api/users/delete";
                _data = this.selected_user;
            }

            $.ajax({
                type: "POST",
                url: _url,
                data: _data,
                success: function(data)
                {
                    location.reload();
                },
                error: function(e)
                {
                    console.log(e);
                }
            });
        }
        function btn_invitation_delete_confirm(invitation)
        {
            this.selected_invitation = invitation;
            $("#popup_confirm").modal();
            $("#popup_confirm_header").html("Confirm delete.");
            $("#popup_confirm_body").html("Are you sure you want to cancel the invitation to " + invitation.email + "?");
            $("#popup_confirm").attr('data-object', 'invitation');
        }
        function btn_user_delete_confirm(user)
        {
            this.selected_user = user;
            $("#popup_confirm").attr('data-backdrop','static');
            $("#popup_confirm").modal();
            $("#popup_confirm_header").html("Confirm delete?");
            $("#popup_confirm_body").html("Are you sure you want to delete " + user.email + "?");
            $("#popup_confirm").attr('data-object', 'user');
        }
        function btn_ok_click()
        {
            location.reload();
        }
        function invitation_reset()
        {
            $("#txt_firstname").val("");
            $("#txt_lastname").val("");
            $("#txt_email").val("");
        }
        function invitation_validate()
        {
            console.log("validate fired!")
            this.errors = [];
            var endpoint = '/api/invitation/validate';
            var invitation = {
                "firstname": $("#txt_firstname").val(),
                "lastname": $("#txt_lastname").val(),
                "email": $("#txt_email").val(),
            } 
            $.ajax({
                type: 'PUT',
                url: endpoint,
                data: invitation,
                success: function(data)
                {
                    $("#lbl_error_firstname").html("");
                    $("#lbl_error_lastname").html("");
                    $("#lbl_error_email").html(""); 
                    $("#btn_invite_send").attr('disabled', false);
                },
                error: function(data)
                {
                    console.log(data);
                },
                statusCode: 
                {                   
                    422: function(data)
                    {
                        console.log('status 422');
                        this.errors = JSON.parse(data.responseText);

                        var e_firstname = (this.errors['firstname'] === undefined) ? '' : this.errors['firstname'];
                        var e_lastname = (this.errors['lastname'] === undefined) ? '' : this.errors['lastname'];
                        var e_email = (this.errors['email'] === undefined) ? '' : this.errors['email'];
                        
                        $("#lbl_error_firstname").html("<small>" + e_firstname + "</small>");
                        $("#lbl_error_lastname").html("<small>" + e_lastname + "</small>");
                        $("#lbl_error_email").html("<small>" + e_email + "</small>"); 
                        $("#btn_invite_send").attr('disabled', true);
                        
                    }
                }
            });                        
        }
    </script>
@endpush





