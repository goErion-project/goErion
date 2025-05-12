@extends('layouts.product')

@section('product-content')


@if(!empty($product->description))
    <p>{!! \GrahamCampbell\Markdown\Facades\Markdown::convertToHtml($product->description) !!}</p>
@else
    <p>No description available.</p>
@endif

@endsection
