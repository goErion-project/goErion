@extends('layouts.app')

@section('title', $category -> name . ' category')

@section('content')
    <div class="row">
        <div class="col-md-3 col-sm-12 bg-gray-500 rounded-3 text-dark border-gray-700 shadow-sm px-0">
            @include('includes.sidebar')
        </div>
        <div class="col-md-9">
            <div class="row">
                <h2 class="mb-3 card rounded p-4 bg-gray-800 fw-bold text-gray-300 text-center">{{ $category -> name}} - category
                </h2>
                <div class="col-md-1 text-lg-right">
                    @include('includes.viewpicker')
                </div>
            </div>
            <hr>

            @if($productsView == 'list')
                @foreach($products as $product)
                    @include('includes.product.row', ['product' => $product])
                @endforeach
            @else
                @foreach($products->chunk(3) as $chunks)
                    <div class="row mt-3">
                        @foreach($chunks as $product)
                            <div class="col-md-4 my-md-0 my-2 col-12">
                                @include('includes.product.card', ['product' => $product])
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @endif

            {{ $products -> links('includes.paginate') }}
        </div>

    </div>

@stop
