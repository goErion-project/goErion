@extends('layouts.profile')

@section('title', 'Wishlist')

@section('profile-content')
    @include('includes.flash.success')
    @include('includes.flash.error')

    <h1 class="mb-3 card rounded p-4 bg-gray-800 fw-bold text-gray-300 text-center">Wishlist</h1>
    <hr>

    @if(auth() -> user() -> whishes -> isNotEmpty())
        <div class="card-columns">
            @foreach(auth() -> user() -> whishes() ->orderByDesc('created_at') -> get() as $whish)
                @include('includes.product.card', ['product' => $whish -> product])
            @endforeach
        </div>
    @else
        <div class="alert alert-warning">Your wishlist of products is empty!</div>
    @endif

@endsection
