@extends('layouts.app')
@section('pagetitle')
	Terms of Service	
@endsection
@section('content_header')
	Terms of Service
@endsection
@section('content')
<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-body">					
				<textarea style="width:100%; height:400px;">{{ Application::terms_of_service() }}</textarea>
			</div>
		</div>
	</div>
</div>
@endsection