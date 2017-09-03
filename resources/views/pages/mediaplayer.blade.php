@extends('layouts.app')
@section('pagetitle')
	Media Player
@endsection
@section('content_header')
	<h4>{{ $filename }}</h4>
@endsection
@section('content')
<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-body">					
				<video width="100%" height="100%" controls autoplay>
				  <source src="/media/video/{{ $filename }}" type="video/mp4">
				</video>
				<br><br>
				<div class="flex-center">
					<a class="btn btn-primary" href="javascript:history.back()" style="width:120px;">Back</a>
				</div>
					
			</div>
		</div>
	</div>
</div>
@endsection