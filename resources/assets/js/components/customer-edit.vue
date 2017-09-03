<template id="customer-edit">
    <form class="form-horizontal" @submit.prevent="customerUpdate" method="POST">
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
        <div v-bind:class="['form-group', errors.altphone ? 'has-error' : '']">
            <label class="col-sm-2 control-label">Mobile:</label>
            <span class="col-sm-6">
                <input type="text" class="form-control" v-model="customer.altphone" v-on:change="validate">
            </span>
            <span class="col-sm-3 no-padding">
                <label class="text-danger control-label"><small>@{{ errors.altphone ? errors.altphone[0] : '' }}</small></label>
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
                <select class="form-control" v-model="customer.billingstate_id" v-on:change="addressToggle" v-on:change="validate">
                    <option v-for="state in states" v-bind:value="state.state_id">@{{ state.stabrv }}</option>
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
                <input type="checkbox" v-model="customer.seperatebillingaddress" v-on:change="addressToggle" style="height:20px; width:20px;"></checkbox>    
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
        <div v-bind:class="['form-group', errors['addresses.0.city'] ? 'has-error' : '']">
        <label class="col-sm-2 control-label">City:</label>
            <span class="col-sm-6">
                <input type="text" class="form-control" v-model="customer.addresses[0].city" v-on:change="validate" :disabled="customer.seperatebillingaddress"/>
            </span>
            <span class="col-sm-3">
                <label class="text-danger control-label"><small>@{{ errors['addresses.0.city'] ? errors['addresses.0.city'][0] : '' }}</small></label>
            </span>
        </div>
        <div v-bind:class="['form-group', errors['addresses.0.adrstate_id'] ? 'has-error' : '']">
            <label class="col-sm-2 control-label">State:</label>
            <span class="col-sm-6">
                <select class="form-control" v-model="customer.addresses[0].adrstate_id" v-on:change="validate" :disabled="customer.seperatebillingaddress">
                    <option v-for="state in states" v-bind:value="state.state_id">@{{ state.stabrv }}</option>
                </select>
            </span>
            <span class="col-sm-3">
                <label class="text-danger control-label"><small>@{{ errors['addresses.0.adrstate_id'] ? errors['addresses.0.adrstate_id'][0] : '' }}</small></label>
            </span>
        </div>
        <div v-bind:class="['form-group', errors['addresses.0.zipcode'] ? 'has-error' : '']">
            <label class="col-sm-2 control-label">Zipcode:</label>
            <span class="col-sm-6">
                <input type="text" class="form-control" v-model="customer.addresses[0].zipcode" v-on:change="validate" :disabled="customer.seperatebillingaddress">
            </span>
            <span class="col-sm-3">
                <label class="text-danger control-label"><small>@{{ errors['addresses.0.zipcode'] ? errors['addresses.0.zipcode'][0] : '' }}</small></label>
            </span>
        </div>
    <br><br>
    <pre>@{{ this.customer }}</pre>         
    </form>   
</template>
<script>
    new Vue({
        el: '#app'       
    })
    Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#token').getAttribute('value');
    Vue.component('customer-edit', {
        template: '#customer-edit-template',
        data: function(){
            return {
                customer: {
                    addresses:[0],
                    appointments:[0],
                    servicequotes:[0]
                },
                states: [],
                paymethods: [],
                errors: []
            };
        },
        created: function(){
            this.paymethodsGet();
            this.statesGet();
            this.customerGet();            
        },
        methods: {
            addressToggle(){
                if(this.customer.seperatebillingaddress)
                {
                    this.customer.addresses[0].address1= this.customer.billingaddress1;
                    this.customer.addresses[0].city = this.customer.billingcity;
                    this.customer.addresses[0].adrstate_id = this.customer.billingstate_id;
                    this.customer.addresses[0].zipcode = this.customer.billingzipcode;
                    this.validate();
                }
            },            
            customerGet: function(){
                this.$http.get('/api/customers/' + {{ $cust_id }}).then(response => {
                    this.customer = response.body;
                }, response => {
                    console.log(response);
                    alert(response.error);
                });
            },
            customerUpdate: function(){
                let endpoint = '/api/customers/' + this.customer.cust_id;
                this.$http.put(endpoint, this.customer).then(response => {
                    console.log(response);
                }, response => {
                    console.log(response);
                    this.errors = response.data;
                });
            },
            paymethodsGet: function(){
                this.$http.get('/api/paymentmethods').then(response => {
                    this.paymethods = response.body;
                }, response => {
                    console.log(response);
                });
            },
            statesGet: function(){
                this.$http.get('/api/states').then(response => {
                    this.states = response.body;
                }, response => {
                    console.log(response);
                });
            },
            validate: function(){
                this.errors = [];
                let endpoint = '/api/customers/' + this.customer.cust_id + '/validate';
                this.$http.put(endpoint, this.customer).then(response => {
                    console.log(response);
                }, response => {
                    console.log(response);
                    this.errors = response.data;
                });
            }

        },
        computed: {
        	fullname: function(){
        		return this.customer.firstname + ' ' + this.customer.lastname + ', ' this.customer.suffix;
        	}
        }
    });
</script>