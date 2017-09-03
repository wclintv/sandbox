@extends('layouts.app')
@section('title')
	SnapDsk
@endsection
@section('content')
<div class="flex-center position-ref full-height">
	<div class="content" style="margin-bottom:15%;">
        <div style="font-weight:300; font-size: 84px">
            <img src="/file/image/snapdsk_logo_1100x300.png" style="width:380px;margin-left:20px;"/>
        </div>
		<div class="links">
		    <a href="{{ URL::to('about') }}">About</a>
		    <a href="{{ URL::to('contact') }}">Contact</a>
		    <a href="{{ URL::to('support') }}">Support</a>
		</div>
	</div>
</div>

@endsection

