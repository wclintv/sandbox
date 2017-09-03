@extends('layouts.app')
@section('title')
    Customers
@endsection
@section('content_header')
    customers
@endsection
@section('content')
    @include('customers.layouts.searchbar')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default panel-h-offset">  
                <div class="panel-body panel-body-overflow">
                    {!! $dataTable->table() !!}
                </div>                
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
