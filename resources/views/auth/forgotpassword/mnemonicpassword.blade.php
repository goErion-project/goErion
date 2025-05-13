@extends('layouts.app')


@section('title','Mnemonic reset')

@section('content')
    <div class="row mt-5 justify-content-center" >
        <div class="col-md-3 text-center">

            <h2 class="mb-3 card rounded p-4 bg-gray-800 fw-bold text-gray-300 text-center">Reset password</h2>

            <div class="card mt-3 p-2 bg-gray-500">
                <p class="mt-3">Please enter your username, mnemonic and your new password</p>

                <form method="POST" action="/forgotpassword/mnemonic">
                    @csrf

                    <div class="form-group ">
                        <input type="text" class="form-control mb-3 bg-danger-subtle hover:bg-gray-200 @if($errors->has('username')) is-invalid @endif" placeholder="Username" name="username" id="username">
                        @if($errors->has('username'))
                            <p class="text-danger">{{$errors->first('username')}}</p>
                        @endif
                    </div>

                    <div class="form-group ">
                        <input type="text" class="form-control mb-4  bg-danger-subtle hover:bg-gray-200 @if($errors->has('mnemonic')) is-invalid @endif" placeholder="Mnemonic" name="mnemonic" id="mnemonic">
                        @if($errors->has('mnemonic'))
                            <p class="text-danger">{{$errors->first('mnemonic')}}</p>
                        @endif
                    </div>

                    <div class="form-row">
                        <div class="col">
                            <input type="password" class="form-control mb-4 bg-danger-subtle hover:bg-gray-200 @if($errors->has('password')) is-invalid @endif" placeholder="New password" name="password"
                                   id="password">
                        </div>
                        <div class="col">
                            <input type="password" class="form-control mb-4 bg-danger-subtle hover:bg-gray-200 @if($errors->has('password')) is-invalid @endif" placeholder="Confirm new password"
                                   name="password_confirmation" id="password_confirm">
                        </div>

                    </div>

                    @if($errors->has('password'))
                        <p class="text-danger">{{$errors->first('password')}}</p>
                    @endif

                    <div class="form-group text-center mb-3">
                        <div class="row">
                            <div class="col-xs-12 col-md-4 offset-md-4">
                                <button type="submit" class="btn btn-outline-dark btn-block" style="margin-top: 15px;">Reset?</button>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
