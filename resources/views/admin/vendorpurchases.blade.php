@extends('layouts.admin')

@section('admin-content')
    <div class="row">
        <div class="col">
            <h4 class="mb-3 card rounded p-4 bg-gray-800 fw-bold text-gray-300 text-center">
                List of Vendor Purchases
            </h4>
            <hr>
        </div>
    </div>
    <div class="card rounded p-4">
        <table class="table">
            <thead>
            <tr>
                <th>Vendor</th>
                <th>
                    <div class="row">
                        <div class="col-md-1">
                            Coin
                        </div>
                        <div class="col-md-3">
                            Amount
                        </div>
                        <div class="col-md-4">
                            Address
                        </div>
                        <div class="col-md-4">
                            Paid at
                        </div>
                    </div>
                </th>
            </tr>
            </thead>
            <tbody>

            @if($vendors->count() == 0 )
                <tr>
                    <td colspan="4" class="text-center">
                        <h4 class="mt-5">No Vendor purchases found found</h4>
                    </td>
                </tr>
            @else
                @foreach($vendors as $vendor)
                    <tr>
                        <td>
                            <strong>{{$vendor->user->username}}</strong>
                        </td>
                        <td>
                            <table class="table table-borderless">
                                @foreach($vendor->user->vendorPurchases as $vendorPurchase)
                                    @if($vendorPurchase->amount > 0)
                                        <div class="row my-1">
                                            <div class="col-md-1">
                                                <span class="badge badge-info">
                                                    {{ strtoupper($vendorPurchase -> coin) }}
                                                </span>
                                            </div>
                                            <div class="col-md-3">
                                                <span class="badge badge-primary">
                                                    {{ $vendorPurchase -> amount }}
                                                </span>
                                            </div>
                                            <div class="col-md-4">
                                                <input class="form-control form-control-sm" readonly="readonly" value="{{ $vendorPurchase -> address }}"/>
                                            </div>
                                            <div class="col-md-4 text-muted">
                                                {{ $vendorPurchase -> updated_at -> diffForHumans() }}
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </table>


                        </td>
                    </tr>
                @endforeach

            @endif

            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="text-center">
                {{$vendors->links()}}
            </div>
        </div>
    </div>


@stop
