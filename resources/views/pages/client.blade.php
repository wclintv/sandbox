@extends('layouts.app')
@section('title')
	Api Explorer
@endsection
@section('content')
	@include('customers.layouts.searchbar')
<datalist id="methods">
	@foreach($methods as $method)
		<option value="{{ $method }}">
	@endforeach
</datalist>
<script type="text/javascript">
	function copyOutputToInput(){
		var data = document.getElementById("output_data").value;
		document.getElementById("input_body").value = data;
	}
</script>
<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">
					<table style="width:100%;">
						<tr>
							<td><h5>Api Explorer<h5></td>
						</tr>
					</table>
				</div>
				<div class="panel-body">
					<form autocomplete="on"  action="apiexplorer" method="POST" enctype="multipart/form-data">
						{{ csrf_field() }}						
						<table>
							<tr >
								<td>Method:</td>
								<td style="padding:2px;"><input name="method" type="text" list="methods" style="width:200px"></td>
							</tr>
							<tr>
								<td>Param:</td>
								<td style="padding:2px;"><input name="param" type="text"></td>
							</tr>
						</table>
						<h4>Request Body:</h4>
						<textarea id="input_body" name="json" style="width:100%; height:100px;"></textarea>
						<input type="submit" style="height:40px;width:120px;" value="Send Request">
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@if(isset($ApiResponse))
<input type="hidden" id="output_data" value='{{ json_encode($ApiResponse->Data, JSON_PRETTY_PRINT) }}'/>
<div>
	<table class="api-output-table">
		<tr>
			<th><h4>QuickBooks Get:</h4></th>
			<th><h4>SnapDsk api:</h4></th>
			<th><h4>QuickBooks Post:</h4></th>
		<tr>
		<tr>
			<td>Status: {{ $qbReport['GetStatus'] or "" }}</td>
			<td>Status: {{ http_response_code() }}</td>
			<td>Status: {{ $qbReport['PostStatus'] or "" }}</td>
		</tr>
		<tr>
			<td>Method: {{ $qbReport['GetMethod'] or "" }}</td>	
			<td>Method: {{ $ApiResponse->Method or "" }} </td>
			<td>Method: {{ $qbReport['PostMethod'] or "" }}</td>
		</tr>
		<tr>
			<td>Requests Sent: {{ $qbReport['GetRequestCount'] or "" }}</td>	
			<td>Param: {{ $ApiResponse->Param or "" }} </td>
			<td>Requests Sent: {{ $qbReport['PostRequestCount'] or "" }}</td>
		</tr>
		<tr>
			<td>Responses Received: {{ $qbReport['GetResponseCount'] or "" }}</td>	
			<td><input type="button" onclick="copyOutputToInput()" value="Copy response data to request" /><br></td>
			<td>Responses Received: {{ $qbReport['PostResponseCount'] or "" }}</td>
		</tr>
		<tr>
			<td>(GET) Response List:</td>
			<td></td>
			<td>(POST) Response List:</td>
		</tr>		
		<tr>
			<td><textarea>{{ $qbReport['GetResponseListStr'] or "" }}</textarea></td>
			<td><textarea>{{ $ApiResponse->pretty_response() }}</textarea></td>
			<td><textarea>{{ $qbReport['PostResponseListStr'] or "" }}</textarea></td>
		</tr>
		<tr>
			<table class="api-output-table">
				<tr>
					<td>(GET) Request List:</td>
					<td>(POST) Request List:</td>
				</tr>
				<tr>
					<td><textarea>{{ $qbReport['GetRequestListStr'] or "" }}</textarea></td>
					<td><textarea>{{ $qbReport['PostRequestListStr'] or "" }}</textarea></td>
				</tr>
			</table>			
		</tr>
	</table>
	<br>		
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
</div>
@endif


@endsection

