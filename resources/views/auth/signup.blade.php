@extends('layouts.app')

@section('title','Sign Up')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow border-1 border-secondary bg-success">
                <h1 class="text-center m-4 fs-1 fw-bold">{{ __('Sign Up') }}</h1>

                <div class="card-body">
                    <form method="POST" action="{{ route('auth.signup.post') }}">
                        @csrf
                        <label for="username" class="form-label">{{ __('Username *') }}</label>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <input type="text" class="form-control bg-danger-subtle @if($errors->has('username')) is-invalid @endif" placeholder="Username" name="username" id="username">
                                @if($errors->has('username'))
                                    <p class="text-danger">{{$errors->first('username')}}</p>
                                @endif
                            </div>
                        </div>
                        <label for="password" class="form-label">{{ __('Password *') }}</label>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <input type="password" class="form-control bg-danger-subtle @if($errors->has('password')) is-invalid @endif" placeholder="Password" name="password"
                                       id="password">
                            </div>
                        </div>
                        <label for="password" class="form-label">{{ __('Confirm Password *') }}</label>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <input type="password" class="form-control bg-danger-subtle @if($errors->has('password')) is-invalid @endif" placeholder="Confirm Password"
                                       name="password_confirmation" id="password_confirm">
                            </div>
                        </div>
                        @if($errors->has('password'))
                            <p class="text-danger">{{$errors->first('password')}}</p>
                        @endif
                        <label for="referral" class="form-label">{{ __('Referral *') }}</label>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="input-group-prepend">
                                    <input type="text" class="form-control bg-danger-subtle" placeholder="Referral Code" name="refid" value="{{ $refid }}"
                                    @if($refid !== '') readonly @endif>
                                </div>
                            </div>
                        </div>

                        @include('includes.captcha')
                        @if($errors->has('captcha'))
                            <p class="text-danger">{{$errors->first('captcha')}}</p>
                        @endif

                        <div class="row mb-3">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Sign Up') }}
                                </button>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="d-grid gap-2">
                                <a href="{{ route('auth.signin') }}" class="btn btn-outline-dark">
                                    {{ __('Already have an account') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
