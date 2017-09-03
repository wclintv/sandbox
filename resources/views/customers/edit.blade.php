@extends('layouts.app')
@section('title')
	{{ $customer_fullname }} - edit
@endsection
@section('content_header')
    {{ $customer_fullname }}
    @if(config('app.debug') == true)
        @if(isset($customer))
            ({{ $customer->cust_id }})
        @endif
    @endif
@endsection
@section('content')
    @include('customers.layouts.searchbar')
    <div class="row">
    	<div class="col-md-8 col-md-offset-2">
    		<div class="panel panel-default panel-h-offset">
    			<div id="panel-body" class="panel-body panel-body-overflow">
    				<customer-edit ref="vue_template"></customer-edit>
    			</div>						
    		</div>
    	</div>
    </div>
@endsection
@section('footer_content')
    <div class="flex-center footer-content">
        <span class="footer-span">  
            <form method="GET" class="no-padding">                          
                <input class="btn btn-default btn-panel-header" style="margin-right:10px;" type="submit" value="Cancel" formaction="/customers/{{ $cust_id }}" />
            </form>                         
        </span>
    	<span class="footer-span">
    		<button id="btn_save" class="btn btn-primary btn-panel-header" >Save</button>
    	</span>

    </div>
@endsection
@push('scripts')
    <template id="customer-edit">
        <div id="edit-form" class="form-horizontal">
            <div v-bind:class="['form-group', errors.title ? 'has-error' : '']">
                <label class="col-sm-2 control-label">Title:</label>
                <span class="col-sm-6">
                    <input type="text" class="form-control" v-model="customer.title" v-on:change="validate">
                </span>
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors.title ? errors.title[0] : '' }}</small></label>
                </span>
            </div>
            <div v-bind:class="['form-group', errors.firstname ? 'has-error' : '']">
                <label class="col-sm-2 control-label">First Name:</label>
                <span class="col-sm-6">
                    <input type="text" class="form-control" v-model="customer.firstname" v-on:change="validate">
                </span>
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors.firstname ? errors.firstname[0] : '' }}</small></label>
                </span>
            </div>
            <div v-bind:class="['form-group', errors.middlename ? 'has-error' : '']">
                <label class="col-sm-2 control-label">Middle Name:</label>
                <span class="col-sm-6">
                    <input type="text" class="form-control" v-model="customer.middlename" v-on:change="validate">
                </span>
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors.middlename ? errors.middlename[0] : '' }}</small></label>
                </span>
            </div>
            <div v-bind:class="['form-group', errors.lastname ? 'has-error' : '']">
                <label class="col-sm-2 control-label">Last Name:</label>
                <span class="col-sm-6">
                    <input type="text" class="form-control" v-model="customer.lastname" v-on:change="validate">
                </span>
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors.lastname ? errors.lastname[0] : '' }}</small></label>
                </span>
            </div>
            <div v-bind:class="['form-group', errors.suffix ? 'has-error' : '']">
                <label class="col-sm-2 control-label">Suffix:</label>
                <span class="col-sm-6">
                    <input type="text" class="form-control" v-model="customer.suffix" v-on:change="validate">
                </span>
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors.suffix ? errors.suffix[0] : '' }}</small></label>
                </span>
            </div>
            <div v-bind:class="['form-group', errors.company ? 'has-error' : '']">
                <label class="col-sm-2 control-label">Company:</label>
                <span class="col-sm-6">
                    <input type="text" class="form-control" v-model="customer.company" v-on:change="validate">
                </span>
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors.company ? errors.company[0] : '' }}</small></label>
                </span>
            </div>
            <div v-bind:class="['form-group', errors.email ? 'has-error' : '']">
                <label class="col-sm-2 control-label">Email:</label>
                <span class="col-sm-6">
                    <input type="text" class="form-control" v-model="customer.email" v-on:change="validate">
                </span>
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors.email ? errors.email[0] : '' }}</small></label>
                </span>
            </div>
            <div v-bind:class="['form-group', errors.phone ? 'has-error' : '']">
                <label class="col-sm-2 control-label">Phone:</label>
                <span class="col-sm-6">
                    <input type="text" class="form-control" v-model="customer.phone" v-on:change="validate">
                </span>
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors.phone ? errors.phone[0] : '' }}</small></label>
                </span>
            </div>
            <div v-bind:class="['form-group', errors.mobilephone ? 'has-error' : '']">
                <label class="col-sm-2 control-label">Mobile:</label>
                <span class="col-sm-6">
                    <input type="text" class="form-control" v-model="customer.mobilephone" v-on:change="validate">
                </span>
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors.mobilephone ? errors.mobilephone[0] : '' }}</small></label>
                </span>
            </div>
            <div v-bind:class="['form-group', errors.fax ? 'has-error' : '']">
                <label class="col-sm-2 control-label">Fax:</label>
                <span class="col-sm-6">
                    <input type="text" class="form-control" v-model="customer.fax" v-on:change="validate">
                </span>
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors.fax ? errors.fax[0] : '' }}</small></label>
                </span>
            </div>
            <div v-bind:class="['form-group', errors.website ? 'has-error' : '']">
                <label class="col-sm-2 control-label">Website:</label>
                <span class="col-sm-6">
                    <input type="text" class="form-control" v-model="customer.website" v-on:change="validate">
                </span>
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors.website ? errors.website[0] : '' }}</small></label>
                </span>
            </div>
            <!--
            <div v-bind:class="['form-group', errors['appointments.0.aptpaymentmethod_id'] ? 'has-error' : '']">
                <label class="col-sm-2 control-label">Pay Method:</label>
                <span class="col-sm-6">
                    <select class="form-control" v-model="customer.appointments[0].aptpaymentmethod_id" v-on:change="addressToggle" v-on:change="validate">
                        <option v-for="paymethod in paymethods" v-bind:value="paymethod.paymentmethod_id">@{{ paymethod.paymentoption }}</option>
                    </select>
                </span>            
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors['appointments.0.aptpaymentmethod_id'] ? errors['appointments.0.aptpaymentmethod_id'][0] : '' }}</small></label>
                </span>
            </div>
            -->
            <div v-bind:class="['form-group', errors.balancedue ? 'has-error' : '']">
                <label class="col-sm-2 control-label">Balance Due:</label>
                <span class="col-sm-6">
                    <label type="text" class="control-label">@{{ customer.balancedue }}</label>
                </span>
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors.balancedue ? errors.balancedue[0] : '' }}</small></label>
                </span>
            </div>      
            <hr>
            <h4>Billing Address:</h4>
            <hr>
            <div v-bind:class="['form-group', errors.billingaddress1 ? 'has-error' : '']">
                <label class="col-sm-2 control-label">Street:</label>
                <span class="col-sm-6">
                    <input class="form-control" type="text" v-model="customer.billingaddress1" v-on:input="addressToggle" v-on:change="validate"/>
                </span>
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors.billingaddress1 ? errors.billingaddress1[0] : '' }}</small></label>
                </span>
            </div>
            <div v-bind:class="['form-group', errors.billingaddress2 ? 'has-error' : '']">
                <label class="col-sm-2 control-label"></label>
                <span class="col-sm-6">
                    <input class="form-control" type="text" v-model="customer.billingaddress2" v-on:input="addressToggle" v-on:change="validate"/>
                </span>
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors.billingaddress2 ? errors.billingaddress2[0] : '' }}</small></label>
                </span>
            </div>
            <div v-bind:class="['form-group', errors.billingcity ? 'has-error' : '' ]">
                <label class="col-sm-2 control-label">City:</label>
                <span class="col-sm-6">
                    <input class="form-control" type="text" v-model="customer.billingcity" v-on:input="addressToggle" v-on:change="validate"/>
                </span>
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors.billingcity ? errors.billingcity[0] : '' }}</small></label>
                </span>
            </div>


            <div v-bind:class="['form-group', errors.billingstate_id ? 'has-error' : '']">
                <label class="col-sm-2 control-label">State:</label>
                <span class="col-sm-6">
                    <select class="form-control" v-model="customer.billingstate_id"  v-on:change="billingStateChange($event.target.value)">
                        <option v-for="state in states" v-bind:value="state.state_id" value="">@{{ state.stabrv }}</option>
                    </select>
                </span>            
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors.billingstate_id ? errors.billingstate_id[0] : '' }}</small></label>
                </span>
            </div>



            <div v-bind:class="['form-group', errors.billingzipcode ? 'has-error' : '']">
                <label class="col-sm-2 control-label">Zipcode:</label>
                <span class="col-sm-6">
                    <input type="text" class="form-control" v-model="customer.billingzipcode" v-on:input="addressToggle" v-on:change="validate"/>
                </span>
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors.billingzipcode ? errors.billingzipcode[0] : '' }}</small></label>
                </span>
            </div>
            <hr>
            <h4>Shipping Address:</h4>
            <hr>
            <div v-bind:class="['form-group', errors.seperatebillingaddress ? 'has-error' : '']">
                <span class="pull-left" style="margin:3px 10px 0px 20px;">
                    <label>Same as Billing Address:</label>                                                             
                </span>
                <span class="pull-left">
                    <input type="checkbox" v-model="customer.seperatebillingaddress" v-on:change="addressToggle" style="height:20px; width:20px;"/>    
                </span>
            </div>
            <div v-bind:class="['form-group', errors['addresses.0.address1'] ? 'has-error' : '']">
                <label class="col-sm-2 control-label">Street:</label>
                <span class="col-sm-6">
                    <input class="form-control" type="text" v-model="customer.addresses[0].address1" v-on:change="validate" :disabled="customer.seperatebillingaddress"/>
                </span>
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors['addresses.0.address1'] ? errors['addresses.0.address1'][0] : '' }}</small></label>
                </span>
            </div>
            <div v-bind:class="['form-group', errors['addresses.0.address2'] ? 'has-error' : '']">
                <label class="col-sm-2 control-label"></label>
                <span class="col-sm-6">
                    <input class="form-control" type="text" v-model="customer.addresses[0].address2" v-on:change="validate" :disabled="customer.seperatebillingaddress"/>
                </span>
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors['addresses.0.address2'] ? errors['addresses.0.address2'][0] : '' }}</small></label>
                </span>
            </div>
            <div v-bind:class="['form-group', errors['addresses.0.city'] ? 'has-error' : '']">
            <label class="col-sm-2 control-label">City:</label>
                <span class="col-sm-6">
                    <input type="text" class="form-control" v-model="customer.addresses[0].city" v-on:change="validate" :disabled="customer.seperatebillingaddress"/>
                </span>
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors['addresses.0.city'] ? errors['addresses.0.city'][0] : '' }}</small></label>
                </span>
            </div>
            <div v-bind:class="['form-group', errors['addresses.0.adrstate_id'] ? 'has-error' : '']">
                <label class="col-sm-2 control-label">State:</label>
                <span class="col-sm-6">
                    <select class="form-control" v-model="customer.addresses[0].adrstate_id" v-on:change="stateChange($event.target.value)" :disabled="customer.seperatebillingaddress">
                        <option v-for="state in states" v-bind:value="state.state_id" value="">@{{ state.stabrv }}</option>
                    </select>
                </span>
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors['addresses.0.adrstate_id'] ? errors['addresses.0.adrstate_id'][0] : '' }}</small></label>
                </span>
            </div>
            <div v-bind:class="['form-group', errors['addresses.0.zipcode'] ? 'has-error' : '']">
                <label class="col-sm-2 control-label">Zipcode:</label>
                <span class="col-sm-6">
                    <input type="text" class="form-control" v-model="customer.addresses[0].zipcode" v-on:change="validate" :disabled="customer.seperatebillingaddress">
                </span>
                <span class="col-sm-3 no-padding">
                    <label class="text-danger control-label"><small>@{{ errors['addresses.0.zipcode'] ? errors['addresses.0.zipcode'][0] : '' }}</small></label>
                </span>
            </div>
            <hr>
            <div v-bind:class="['form-group', errors['servicequotes.0.notes'] ? 'has-error' : '']">
                <label class="col-sm-2 control-label">Notes:</label>
                <span class="col-sm-6">
                    <textarea type="text" class="form-control" style="height: 200px;" v-model="customer.servicequotes[0].notes" v-on:change="validate"></textarea>
                </span>
            </div>
            @if(config('app.debug') == true)
            <br><br>
            <pre>@{{ this.customer }}</pre> 
            @endif        
        </div>   
    </template>    

    <script>
        Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#token').getAttribute('value');
        var customer_edit = Vue.extend({
            template: '#customer-edit',
            data: function(){
                return {
                    states: [],
                    paymethods: [],                    
                    customer: {
                        addresses:[{}],
                        appointments:[{}],
                        servicequotes:[{}]
                    },
                    errors: [],
                    loaded: false,
                    previousBillingStateId: null,
                    previousStateId: null,
                };
            },
            created: function(){
                this.statesGet();
                //this.paymethodsGet();
                this.customerGet();   
                this.loaded = true;
            },
            computed: {
                _fullname: function(){
                    return this.customer.firstname + ' ' + this.customer.lastname
                },
            },
            methods: {
                addressToggle: function(){
                    if(this.customer.seperatebillingaddress)
                    {
                        this.customer.addresses[0].address1= this.customer.billingaddress1;
                        this.customer.addresses[0].address2 = this.customer.billingaddress2;
                        this.customer.addresses[0].city = this.customer.billingcity;
                        this.customer.addresses[0].adrstate_id = this.customer.billingstate_id;
                        this.customer.addresses[0].zipcode = this.customer.billingzipcode;
                    }
                    this.validate();
                },  
                billingStateChange: function(value){
                    if(this.customer.billingstate_id == null)
                    {
                        this.customer.billingstate_id = this.previousBillingStateId;                
                    }
                    this.addressToggle();
                },       
                customerGet: function(){
                    this.$http.get('/api/customers/' + {{ $cust_id }}).then(function(response){
                        this.customer = response.body;    
                        this.previousBillingStateId = this.customer.billingstate_id;
                        this.previousStateId = this.customer.addresses[0].adrstate_id;          
                    }, function(response) {
                        console.log(response);
                        alert(response.error);
                    });
                },
                customerUpdate: function(){
                    $("#popup_loader").modal({backdrop:"static", keyboard: false});
                    let endpoint = '/api/customers/' + this.customer.cust_id;
                    this.$http.put(endpoint, this.customer).then(function(response){
                        console.log('update successful.');
                        $("#popup_loader").modal('toggle');
                        window.location.href = "/customers/" + this.customer.cust_id;
                    }, function(response){
                        console.log(response);                        
                        this.errors = response.data;
                        $("#popup_loader").modal('toggle');
                    });
                },
                paymethodsGet: function(){
                    this.$http.get('/api/paymentmethods').then(function(response){
                        this.paymethods = response.body;
                    }, function(response){
                        console.log(response);
                    });
                },
                stateChange: function(value){
                    if(this.customer.addresses[0].adrstate_id == null)
                    {
                        this.customer.addresses[0].adrstate_id = this.previousStateId; 
                    }                                       
                },                
                statesGet: function(){
                    this.$http.get('/api/states').then(function(response){
                        this.states = response.body;
                    }, function(response){
                        console.log(response);
                    });
                },
                validate: function(){
                    if(this.loaded == true){
                        console.log('validate fired');
                        this.errors = [];
                        let endpoint = '/api/customers/validate';
                        this.$http.put(endpoint, this.customer).then(function(response){
                            console.log(response);
                        }, function(response){
                            console.log(response);
                            this.errors = response.data;
                        });                        
                    }
                }
            },
        });
        var vm = new Vue({
            el: '#panel-body',
            components: {
                'customer-edit': customer_edit
            },
        }); 

        //bind the external button in the footer to the vue component method.
        document.getElementById("btn_save").onclick = function () {
            vm.$refs.vue_template.customerUpdate();
        };
    </script>
@endpush





