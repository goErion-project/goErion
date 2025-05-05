{{--@search--}}
<div class="d-flex justify-content-center flex-grow-1 mx-lg-4">
    <form action="{{ route('search') }}" method="POST" class="d-flex" role="search" style="max-width: 600px; width: 100%;">
        @csrf
        <div class="input-group">
            <button class="btn btn-outline-dark bg-white hover:text-yellow-500 px-4" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                All
            </button>
            <ul class="dropdown-menu bg-white">
                <li><a class="dropdown-item" href="#">Action</a></li>
                <li><a class="dropdown-item" href="#">Another action</a></li>
                <li><a class="dropdown-item" href="#">Something else here</a></li>
                <li><a class="dropdown-item" href="#">Separated link</a></li>
            </ul>
            <input class="form-control" type="text" placeholder="Search" id="search"
                   name="Search" value="{{app('request')->input('query')}}">
            <button class="btn btn-warning px-4" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>
</div>
{{--@endsearch--}}
