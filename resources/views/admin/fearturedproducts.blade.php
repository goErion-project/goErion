@extends('layouts.admin')

@section('admin-content')

    @isModuleEnabled('FeaturedProducts')
    @include('featuredproducts::featuredproductsview')
    @endisModuleEnabled


@stop
