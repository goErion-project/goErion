@extends('layouts.app')

@section('title','Home Page')

@section('content')
    <div class="container-fluid p-0">
        <div class="row mx-0 w-100">
            <div class="col-md-3 col-sm-12 bg-dark rounded text-dark shadow px-0">
                @include('includes.sidebar')
            </div>
            <div class="col-md-9 col-sm-12 ps-4 pe-0 pt-4">
                @include('includes.main')
            </div>
        </div>
    </div>
@endsection
