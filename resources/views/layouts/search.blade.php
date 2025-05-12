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
                @foreach ($categories as $category)
                    <li>
                        <a class="dropdown-item" href="{{ url('/search?query=' . request('query') . '&category=' . $category->id) }}">
                            {{ $category->name }}
                        </a>
                    </li>
                    
                @endforeach            
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