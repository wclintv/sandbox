@extends('layouts.app')
@section('pagetitle')
	Intuit Requirements
@endsection
@section('content_header')
    Intuit App Requirements
@endsection
@section('content')
<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-body">
				<div style="width:200px; margin: 0 auto;">
					<p><a class="btn btn-primary" href="/requirements/technical" style="width: 100%;">Technical Requirements</a></p>
					<p><a class="btn btn-primary" href="/requirements/security" style="width: 100%;">Security Requirements</a></p>
					<a class="btn btn-primary" href="/requirements/marketing" style="width: 100%;">Marketing Requirements</a>
				</div>
			
			</div>
		</div>
	</div>
</div>
@endsection

