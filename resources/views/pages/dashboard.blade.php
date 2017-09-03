@extends('layouts.app')
@section('content')
@include('layouts.menu')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    Welcome! {{ Auth::user()->name }} ({{ Auth::user()->email }})<br><br>
                    <a class="links" href="client">Developer Client</a><br>  
                    <a class="links" href="customers">Customers</a><br>
                    <a class="links" href="about">About</a><br>  
                    <a class="links" href="contact">Contact</a><br>     
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
