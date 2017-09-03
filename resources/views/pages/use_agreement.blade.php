@extends('layouts.app')
@section('pagetitle')
	Use Agreement
@endsection
@section('content_header')
	Use Agreement
@endsection
@section('content')
<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-body">					
				<textarea readonly="true" style="width:100%; height: 400px;">{{ $use_agreement }}</textarea>
			</div>
		</div>
	</div>
</div>
@endsection
@section('footer')

@endsection