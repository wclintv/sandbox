@extends('layouts.app')
@section('title')
	Marketing Requirements
@endsection
@section('content_header')
    Marketing Requirements
@endsection
@section('content')
<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-body">
				<h3>App card review</h3>
				<table class="table">
					<tbody>
						<tr>
							<td>1</td>
							<td>Intuit logo and branding guidelines are followed correctly</td>
							<td style="text-align: center;"><i class="fa fa-check" aria-hidden="true" style="color: green; font-size: 24px;"></i></td>
						</tr>
						<tr>
							<td>2</td>
							<td>The customer experience with the app​ on Apps.com is easy and positive.</td>
							<td style="text-align: center;"><i class="fa fa-check" aria-hidden="true" style="color: green; font-size: 24px;"></i></td>
						</tr>
						<tr>
							<td>3</td>
							<td>Your app card is complete and the content is helpful.</td>
							<td style="text-align: center;"><i class="fa fa-check" aria-hidden="true" style="color: green; font-size: 24px;"></i></td>
						</tr>
						<tr>
							<td>4</td>
							<td>Site visitors can understand what the app does and its benefits to QuickBooks Online users.</td>
							<td style="text-align: center;"><a href="/mediaplayer/video/4_marketing.mp4">Demo</a></td>
						</tr>
						<tr>
							<td>5</td>
							<td>All links within your app work.</td>
							<td style="text-align: center;"><i class="fa fa-check" aria-hidden="true" style="color: green; font-size: 24px;"></i></td>
						</tr>
						<tr>
							<td>6</td>
							<td>You offer support and your contact info is correct.</td>
							<td style="text-align: center;"><i class="fa fa-check" aria-hidden="true" style="color: green; font-size: 24px;"></i></td>
						</tr>
						<tr>
							<td>7</td>
							<td>You can verify any claims made on the app card. For example, if you claim it is “The #1 CRM app,” be prepared to share evidence to back it up.</td>
							<td style="text-align: center;"><i class="fa fa-check" aria-hidden="true" style="color: green; font-size: 24px;"></i></td>
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
