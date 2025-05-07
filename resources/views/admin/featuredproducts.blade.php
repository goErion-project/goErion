@extends('layouts.admin')

@section('admin-content')
    <div class="card rounded p-4">
        @isModuleEnabled('FeaturedProducts')
        @include('featuredproducts::featuredproductsview')
        @endisModuleEnabled
    </div>

@endsection
