@extends('layouts.app')
@section('pagetitle')
	Privacy Policy
@endsection
@section('content_header')
	Privacy Policy
@endsection
@section('content')
<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-body">					
				<textarea readonly="true" style="width:100%; height:400px;">{{ $privacy_policy }}</textarea>
			</div>
		</div>
	</div>
</div>
@endsection