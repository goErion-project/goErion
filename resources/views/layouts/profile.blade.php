@extends('layouts.app')

@section('title','Profile settings')

@section('content')
    <div class="row">
        <div class="col-md-3 px-0 card bg-gray-500 rounded-3 shadow-sm">
            @include('includes.profile.menu')
        </div>
        <div class="col-md-9 ps-4">
            @yield('profile-content')
        </div>
    </div>
@endsection
