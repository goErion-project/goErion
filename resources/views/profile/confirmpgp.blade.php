@extends('layouts.profile')

@section('profile-content')

    @include('includes.flash.error')

    <h1 class="mb-3 card rounded p-4 bg-gray-800 fw-bold text-gray-300 text-center">Confirm your PGP key:</h1>
    <hr>
    <div class="form-group">
        <label for="decrypt_message">Decrypt this message:</label>
        <textarea name="decrypt_message" id="decrypt_message" class="form-control " rows="10" style="resize: none;"  readonly>{{{ session() -> get(\App\Marketplace\PGP::NEW_PGP_ENCRYPTED_MESSAGE) }}}</textarea>
        <p class="text-muted">Decrypt this message and get validation number.</p>
    </div>
    <form method="POST" action="{{ route('profile.pgp.store') }}" class="form-inline">
        @csrf
        <label for="validation_number">Validation number:</label>
        <input type="number" class="form-control mx-2" required name="validation_number" id="validation_number"/>
        <button class="btn btn-outline-success">Validate PGP</button>

    </form>
@endsection
