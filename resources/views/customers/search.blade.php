@extends('layouts.app')
@section('content')
@include('layouts.menu')
<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">
					Customer Search
				</div>
				<div class="panel-body">					
					<form method="POST">
						{{ csrf_field() }}
					ID: <input type="text" name="param"/>
					<input type=submit value="Search"/>

					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection