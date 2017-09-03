<!doctype html>
<html lang="en">
	<head>
	  	<meta charset="utf-8">
		<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">	</head>
		<style type="text/css">
			body {
				font-family: Raleway;
			}
			.flex-center {
				align-items: center;
				display: flex;
				justify-content: center;
			}
		</style>
	</head>	
	<body>
		<div class="container">
			<div class="flex-center">
				<img src="{{ url('/application/icon') }}" style="width: 60px; height: 60px; min-width: 60px; margin-right: 10px;">
				<h3>{{ $email->from_name }} has invited you to join SnapDsk.  </h3>
			</div>
			<p>Please follow the link below to complete your registration:</p>
			<div class="flex-center">	
					<a class="btn btn-primary" target="_blank" href="{{ $callback_url }}">Complete Registration</a>		
			</div>
			<br>
			<p>This invitation expires: {{ $invitation->expires() }}</p>
		</div>
	</body>
</html>



