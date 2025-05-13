@extends('layouts.app')


@section('title','Forgot Password')

@section('content')

    <div class="row mt-5 justify-content-center" >
        <div class="col-md-4 text-center">
            <h2 class="mb-3 card rounded p-4 bg-gray-800 fw-bold text-gray-300 text-center">Forgot your password?</h2>
            <div class="alert alert-warning">
                Note that you will not be able to read messages encrypted by the key from previous password.
            </div>
            <div class="card bg-gray-500 p-2 mt-3">
                <p>Please choose how to recover it</p>

                <form method="GET" action="/forgotpassword/pgp">
                    <div class="form-group text-center">
                        <div class="row">
                            <button type="submit" class="btn btn-outline-dark btn-block mb-4">PGP</button>
                        </div>
                    </div>
                </form>

                <form method="GET" action="/forgotpassowrd/mnemonic">
                    <div class="form-group text-center">
                        <div class="row">
                            <button type="submit" class="btn btn-outline-dark btn-block mb-3">Mnemonic</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection
