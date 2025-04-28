@extends('layouts.app')

@section('title','Mnemonic')

@section('content')
    <div class="row mb-3 justify-content-center">
        <div class="col-md-4">
            <label for="mnemonic" class="form-label">{{ __('Mnemonic *') }}</label>
            <div class="form-floating">
                <p>
                    This is your mnemonic key. It consists out of {{config('marketplace.mnemonic_length')}} words.
                    Please write
                    them down. This is the only time they will be shown to you, and without them you cannot recover
                    your account
                    in case you lose password.
                </p>
            </div>
            <div class="form-floating mb-4">
            <textarea
                class="form-control py-3 mt-4"
                id="mnemonic"
                name="mnemonic"
                style="height: 150px; font-size: 16px; line-height: 1.5;"
                readonly
            >{{ $mnemonic }}</textarea>
                <label for="mnemonic" class="">Mnemonic</label>
            </div>
            <div class="form-floating">
                <a href="{{ route('auth.signin') }}" class="btn btn-warning">Proceed to Sign In</a>
            </div>
        </div>
    </div>
@endsection
