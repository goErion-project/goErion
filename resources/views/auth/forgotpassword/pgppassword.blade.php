@extends('layouts.app')


@section('title','Forgot Password')

@section('content')
    <div class="row mt-5 justify-content-center" >
        <div class="col-md-6 text-center">

            <h2 class="mb-3 card rounded p-4 bg-gray-800 fw-bold text-gray-300 text-center">Reset password</h2>

            <div class="mt-3">

                <p>Please enter your username</p>

                <form method="post" action="/forgotpassword/pgp">
                    @csrf

                    <div class="form-group ">
                        <input type="text" class="form-control @if($errors->has('username')) is-invalid @endif" placeholder="Username" name="username" id="username">
                        @if($errors->has('username'))
                            <p class="text-danger">{{$errors->first('username')}}</p>
                        @endif
                    </div>
                    @include('includes.flash.error')
                    <div class="form-group text-center">
                        <div class="row">
                            <div class="col-xs-12 col-md-4 offset-md-4">
                                <button type="submit" class="btn btn-outline-primary btn-block" style="margin-top: 15px;">Send</button>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
