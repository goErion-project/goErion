@extends('layouts.app')

@section('title','Home Page')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-sm-12 bg-dark rounded text-dark shadow">
                @include('includes.sidebar')
            </div>
            <div class="col-md-9 col-sm-12">
                @include('includes.main')
            </div>
        </div>
    </div>
@endsection
