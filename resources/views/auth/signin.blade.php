@extends('layouts.app')

@section('title','Sign In')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow border-1 border-secondary bg-success">
                    <h1 class="text-center m-4 fs-1 fw-bold">{{ __('Sign In') }}</h1>

                    <div class="card-body">
                        <form method="POST" action="{{ route('auth.signin.post') }}">
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
                                    <input type="password" class="form-control bg-danger-subtle @error('password',$errors) is-invalid @enderror" placeholder="Password" name="password"
                                           id="password">
                                    @error('password',$errors)
                                    <p class="text-danger">{{$errors->first('password')}}</p>
                                    @enderror
                                </div>
                            </div>
                            @include('includes.captcha')
                            <div class="row mb-3">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Sign In') }}
                                    </button>
                                </div>
                            </div>
                            @include('includes.flash.error')
                        </form>
                        <hr>
                        <div class="row mb-3">
                            <div class="d-grid gap-2">
                                <a href="/forgotpassword" class="btn btn-outline-dark">
                                    {{ __('Forgot Password?') }}
                                </a>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="d-grid gap-2">
                                <a href="{{ route('auth.signup') }}" class="btn btn-outline-dark">
                                    {{ __('Create your GoErion account') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
