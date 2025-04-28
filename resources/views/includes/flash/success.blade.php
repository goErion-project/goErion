@if(count($errors) > 0)
    @foreach($errors->all() as $error)
        <div class="d-flex invalid-feedback bg-danger text-white justify-content-center p-2 rounded">
            {{ $error }}
        </div>
    @endforeach
@endif
