@extends('layouts.app')
@section('title')
	Technical Requirements
@endsection
@section('content_header')
    Technical Requirements
@endsection
@section('content')
<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-body">
				<h3>UI components</h3>
				<table class="table">
					<tbody>
						<tr>
							<td>1.1</td>
							<td>The 'Connect to QuickBooks' widget shows in the app prior to connection, and is hidden once a connection has been established.</td>
							<td><a href="/mediaplayer/video/1.1.mp4">Demo</a></td>
						</tr>
						<tr>
							<td>1.2</td>
							<td>Widgets/Buttons/Workflows work in Internet Explorer 11 (or later), as well as Firefox and Chrome (current versions).</td>
							<td><a href="/mediaplayer/video/1.2.mp4">Demo</a></td>
						</tr>
						<tr>
							<td>1.3</td>
							<td>Only approved Apps.com, QuickBooks, and Intuit images are used.</td>
							<td><a href="/mediaplayer/video/1.3.mp4">Demo</a></td>
						</tr>
						<tr>
							<td>1.4</td>
							<td>QuickBooks is spelled properly (including capitalization); no abbreviations are permitted.</td>
							<td><a href="/mediaplayer/video/1.4.mp4">Demo</a></td>
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
				<h3>Quickbooks data connection</h3>
				<table class="table">
					<tbody>
						<tr>
							<td>2.1</td>
							<td>The API successfully passes data between your app and QuickBooks Online. Only QuickBooks API calls are used to move data.</td>
							<td><a href="/mediaplayer/video/2.1.mp4">Demo</a></td>
						</tr>
						<tr>
							<td>2.2</td>
							<td>Once connected through OAuth, the connection is maintained until the user disconnects from QuickBooks. Signing out of an app does not disconnect a company.</td>
							<td><a href="/mediaplayer/video/2.2.mp4">Demo</a></td>
						</tr>
						<tr>
							<td>2.3</td>
							<td>A user can disconnect the app from QuickBooks from within your app.</td>
							<td><a href="/mediaplayer/video/2.3.mp4">Demo</a></td>
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
				<h3>Sign in with Intuit Button (OpenID)</h3>
				<table class="table">
					<tbody>
						<tr>
							<td>3.1</td>
							<td>The Sign in with Intuit button appears on all app sign-in pages and is displayed properly. When clicked, it launches the Intuit OpenID sign-in page.</td>
							<td><a href="/mediaplayer/video/3.1.mp4">Demo</a></td>
						</tr>
						<tr>
							<td>3.2</td>
							<td>A new unknown user who clicks Sign in with Intuit signs in only to the app, without executing OAuth.</td>
							<td><a href="/mediaplayer/video/3.2.mp4">Demo</a></td>
						</tr>
						<tr>
							<td>3.3</td>
							<td>An existing connected user who clicks Sign in with Intuit is taken into the app and data service calls work.</td>
							<td><a href="/mediaplayer/video/3.3.mp4">Demo</a></td>
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
				<h3>Interaction with Apps.com</h3>
				<table class="table">
					<tbody>
						<tr>
							<td>4.1</td>
							<td>On Apps.com, a new user can sign up for a free trial of your app by clicking the Get app now button.</td>
							<td><a href="/mediaplayer/video/4.1.mp4">Demo</a></td>
						</tr>
						<tr>
							<td>4.2</td>
							<td>If a user is signed in to Apps.com but not signed in to your app, the user can sign in to your app without being asked for credentials.</td>
							<td><a href="/mediaplayer/video/4.2.mp4">Demo</a></td>
						</tr>
						<tr>
							<td>4.3</td>
							<td>If the user has not signed out of your app or Apps.com, your app should launch from Apps.com without asking for credentials.</td>
							<td><a href="/mediaplayer/video/4.3.mp4">Demo</a></td>
						</tr>
						<tr>
							<td>4.4</td>
							<td>A user can disconnect your app's access to their QuickBooks account from Apps.com.</td>
							<td><a href="/mediaplayer/video/4.4.mp4">Demo</a></td>
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
