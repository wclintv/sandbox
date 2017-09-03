@extends('layouts.app')
@section('pagetitle')
	Security Requirements
@endsection
@section('content_header')
    Security Requirements
@endsection
@section('content')
<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-body">
				<h3>App server configuration</h3>
				<table class="table">
					<tbody>
						<tr>
							<td>1</td>
							<td>Caching has been disabled on all SSL pages and all pages that contain sensitive data by using value no-cache and no-store instead of private in the Cache-Control header.</td>
							<td><a href="/mediaplayer/video/1_App_Server_Configuration.mp4">Demo</a></td>
						</tr>
						<tr>
							<td>2</td>
							<td>All OS, web server, and app server security patches are up to date at this time, and that new patches are applied in a commercially reasonable timeframe after they are made available by the hardware and software vendors.</td>
							<td><a href="/mediaplayer/video/2_App_Server_Configuration.mp4">Demo</a></td>
						</tr>
						<tr>
							<td>3</td>
							<td>SSL must be configured to support only TLS version 1.1 or higher. TLS version 1.2 using AES 128 or higher with SHA-256 is recommended.</td>
							<td><a href="/mediaplayer/video/3_App_Server_Configuration.mp4">Demo</a></td>
						</tr>
						<tr>
							<td>4</td>
							<td>HTTPS is enforced on all pages of your app.</td>
							<td><a href="/mediaplayer/video/4_App_Server_Configuration.mp4">Demo</a></td>
						</tr>
						<tr>
							<td>5</td>
							<td>The app web server must be configured to disable the TRACE and other HTTP methods if not being used.</td>
							<td><a href="/mediaplayer/video/5_App_Server_Configuration.mp4">Demo</a></td>
						</tr>
						<tr>
							<td>6</td>
							<td>You must not log any user’s credentials or QuickBooks data.</td>
							<td><a href="/mediaplayer/video/6_App_Server_Configuration.mp4">Demo</a></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-body">
				<h3>Attack vulnerability</h3>
				<table class="table">
					<tbody>
						<tr>
							<td>1</td>
							<td>Cross Site Request Forgery.</td>
							<td style="text-align: center;"><a href="/mediaplayer/video/1_Attack_Vulnerability.mp4">Demo</a></td>
						</tr>
						<tr>
							<td>2</td>
							<td>Cross Site Scripting (including reflected and stored cross site scripting).</td>
							<td style="text-align: center;"><a href="/mediaplayer/video/2_Attack_Vulnerability.mp4">Demo</a></td>
						</tr>
						<tr>
							<td>3</td>
							<td>SQL Injection</td>
							<td style="text-align: center;"><a href="/mediaplayer/video/3_Attack_Vulnerability.mp4">Demo</a></td>
						</tr>
						<tr>
							<td>4</td>
							<td>XML Injection.</td>
							<td style="text-align: center;"><a href="/mediaplayer/video/4_Attack_Vulnerability.mp4">Demo</a></td>
						</tr>
						<tr>
							<td>5</td>
							<td>Authentication, Sessions Management and Functional level access control (if any).</td>
							<td style="text-align: center;"><a href="/mediaplayer/video/5_Attack_Vulnerability.mp4">Demo</a></td>
						</tr>
						<tr>
							<td>6</td>
							<td>Forwards or Redirects in use have been validated.</td>
							<td style="text-align: center;"><i class="fa fa-check" aria-hidden="true" style="color: green; font-size: 24px;"></i></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-body">
				<h3>QuickBooks data usage</h3>
				<table class="table">
					<tbody>
						<tr>
							<td>1</td>
							<td>Your app does not provide third-parties with access to a customer's QuickBooks data, via external API calls or any other means.</td>
							<td style="text-align: center;"><i class="fa fa-check" aria-hidden="true" style="color: green; font-size: 24px;"></i></td>
						</tr>
						<tr>
							<td>2</td>
							<td>Your app cannot export, save, or store QuickBooks data for any purpose other than the functional use of your app.</td>
							<td style="text-align: center;"><i class="fa fa-check" aria-hidden="true" style="color: green; font-size: 24px;"></i></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-body">
				<h3>App source code</h3>
				<table class="table">
					<tbody>
						<tr>
							<td>1</td>
							<td>All app session cookies have the following attributes set: Secure, HTTPOnly</td>
							<td style="text-align: center;"><a href="/mediaplayer/video/1_App_Source_Code.mp4">Demo</a></td>
						</tr>
						<tr>
							<td>2</td>
							<td>No Intuit OAuth Token or customer-identifying information should be exposed within your app.</td>
							<td style="text-align: center;"><i class="fa fa-check" aria-hidden="true" style="color: green; font-size: 24px;"></i></td>
						</tr>
						<tr>
							<td>3</td>
							<td>Once a user completes the OAuth login process, your app must store only the consumer key, consumer secret, access token, realmId/companyId, and access token secret and encrypt them</td>
							<td style="text-align: center;"><a href="/mediaplayer/video/3_App_Source_Code.mp4">Demo</a></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection
@section('footer_content')
    <div class="flex-center footer-content">
    	<span style="margin-right:10px;">    		
			<a class="btn btn-primary" href="/requirements">Back to Index</a>
    	</span>
    </div>
@endsection
