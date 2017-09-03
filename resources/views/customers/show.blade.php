@extends('layouts.app')
@section('title')
	{{ $customer->full_name() }} 
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
			@if(isset($customer))
			<div id="panel-body" class="panel-body panel-body-overflow">
				<div class="row line-height">
					<div class="col-xs-2 col-min text-right">Company:</div>
					{{ $customer->company }}
				</div>
				<div class="row line-height">
					<div class="col-xs-2 col-min text-right">Phone:</div>
					{{ $customer->phone or "" }}
				</div>
				<div class="row line-height">
					<div class="col-xs-2 col-min text-right">Mobile:</div>
					{{ $customer->mobilephone or "" }}
				</div>
				<div class="row line-height">
					<div class="col-xs-2 col-min text-right">Fax:</div>
					{{ $customer->fax or "" }}
				</div>
				<div class="row line-height">
					<div class="col-xs-2 col-min text-right">Email:</div>
					{{ $customer->email or ""}}
				</div>
				<div class="row line-height">
					<div class="col-xs-2 col-min text-right" >Website:</div>
					<div class="col-xs-2 no-padding text-left"><a class="link" href="{{ $customer->website }}">{{ $customer->website or ""}}</a></div>
				</div>
				<!--
				<div class="row line-height">
					<div class="col-xs-2 col-min text-right">Pay Method:</div>
					{{ $customer->appointments[0]->paymentmethod->paymentoption or "" }}<br>
				</div>
				-->
				<div class="row line-height">
					<div class="col-xs-2 col-min text-right">Balance Due:</div>
					{{ $customer->balancedue }}
				</div>
				<hr>
				<div class="row">
					<div class="col-xs-2 col-min text-right">
						Billing Address:
					</div>
					<div class="col-xs-7 no-padding text-left">
						{{ $customer->billingaddress1 or "" }}, {{ $customer->billingaddress2 or "" }}  <br>
						{{ $customer->billingcity or "" }},
						{{ $customer->billingstate->stabrv or "" }},
						{{ $customer->billingzipcode or "" }}

					</div>			
					
				</div>
				<hr>
				<div class="row">
					<div class="col-xs-2 col-min text-right">
						Shipping Address:
					</div>
					<div class="col-xs-7 no-padding text-left">
						{{ $customer->addresses[0]->address1 or "" }}, {{ $customer->addresses[0]->address2 or "" }} 	<br>
						{{ $customer->addresses[0]->city or "" }},
						{{ $customer->addresses[0]->state->stabrv or "" }},
						{{ $customer->addresses[0]->zipcode or "" }}	
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-xs-2 col-min text-right">
						Notes:
					</div>
					<div class="col-xs-7 no-padding">
						<textarea class="form-control" readonly="true" style="height: 200px; background-color: white;">{{ $customer->servicequotes[0]->notes or "" }}</textarea>						
					</div>
				</div>

				@if(config('app.debug') == true)
	            	<br><br>
	            	<pre>{{ $customer->json_pretty() }}</pre> 
            	@endif  
			</div>
			@else
				@if(isset($apierror))
				<div class="panel-heading" style="display: inline-block; width:100%">
					Error:
				</div>
				<div class="panel-body">
					{{ $apierror->message }}
				</div>
				@endif
			@endif
		</div>
	</div>
</div>
@endsection
@section('footer_content')
    <div class="flex-center footer-content">
		<span class="footer-span">
			<form method="GET">
				<input class="btn btn-primary btn-panel-header" type="submit" value="Edit" formaction="/customers/{{ $customer->cust_id }}/edit"/>
			</form>	
		</span>
	</div>
@endsection