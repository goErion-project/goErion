@extends('layouts.profile')

@section('profile-content')
    @include('includes.flash.error')
    @include('includes.flash.success')

    <h1 class="mb-3 card rounded p-4 bg-gray-800 fw-bold text-gray-300 text-center">Banned account</h1>

    <div class="alert alert-danger text-center">
        You are banned until {{ $until }}.
    </div>

@endsection
