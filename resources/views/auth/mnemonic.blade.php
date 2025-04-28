@extends('layouts.app')

@section('title','Mnemonic')

@section('content')
    <label for="mnemonic" class="form-label">{{ __('Mnemonic *') }}</label>
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="form-floating">
                <p>
                    This is your mnemonic key. It consists out of {{config('marketplace.mnemonic_length')}} words.
                    Please write
                    them down. This is the only time they will be shown to you, and without them you cannot recover
                    your account
                    in case you lose password.
                </p>
            </div>
            <div class="form-floating">
                <textarea class="form-control" id="" name="" rows="10" readonly>{{ $mnemonic }}</textarea>
                <label for="mnemonic">Mnemonic</label>
            </div>
            <div class="form-floating">
                <a href="{{ route('auth.signin') }}" class="btn btn-warning">Proceed to Sign In</a>
            </div>
        </div>
    </div>
@endsection
