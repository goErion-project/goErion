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




{{-- @search--}}
{{-- <div class="d-flex justify-content-center flex-grow-1 mx-lg-4"> --}}
    {{-- <form action="{{ route('search') }}" method="GET" class="d-flex" role="search" style="max-width: 600px; width: 100%;"> --}}
        {{-- <div class="input-group"> --}}
            <!-- Dropdown for Categories -->
            {{-- @php --}}
                {{-- $selectedCategory = request('category') ? \App\Models\Category::find(request('category')) : null; --}}
            {{-- @endphp --}}
            {{-- <div class="dropdown"> --}}
                {{-- <button class="btn btn-outline-dark bg-white hover:text-yellow-500 px-4 dropdown-toggle" type="button" --}}
                        {{-- id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false"> --}}
                    {{-- {{ $selectedCategory ? $selectedCategory->name : 'All' }} --}}
                {{-- </button> --}}
                {{-- <ul class="dropdown-menu" aria-labelledby="categoryDropdown"> --}}
                    {{-- <li><a class="dropdown-item" href="{{ url('/search?query=' . request('query')) }}">All</a></li> --}}
                    {{-- @foreach($categories as $category) --}}
                        {{-- <li> --}}
                            {{-- <a class="dropdown-item" href="{{ url('/search?query=' . request('query') . '&category=' . $category->id) }}"> --}}
                                {{-- {{ $category->name }} --}}
                            {{-- </a> --}}
                        {{-- </li> --}}
                    {{-- @endforeach --}}
                {{-- </ul> --}}
            {{-- </div> --}}

            <!-- Search Input -->
            {{-- <input class="form-control" type="text" placeholder="Search" id="search" --}}
                   {{-- name="query" value="{{ request('query') }}"> --}}

            {{-- <!-- Submit Button --> --}}
            {{-- <button class="btn btn-warning px-4" type="submit"> --}}
                {{-- <i class="fas fa-search"></i> --}}
            {{-- </button> --}}
        {{-- </div> --}}
    {{-- </form> --}}
{{-- </div> --}}
{{-- @endsearch --}}