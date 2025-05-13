@extends('layouts.product')

@section('product-content')


@if(!empty($product->rules))
<p>{!! \GrahamCampbell\Markdown\Facades\Markdown::convertToHtml($product->rules) !!}</p>
@else
<p>No rules available.</p>
@endif


@endsection
