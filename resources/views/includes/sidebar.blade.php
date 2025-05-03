@auth
    <div class="m-2 card shadow bg-gray-500">
        <div class="d-flex align-items-start gap-3 p-3 rounded-3">
            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" alt="User Avatar" class="rounded-circle" style="width: 55px; height: 55px; object-fit: cover;">
                @else
                    <span class="text-white fw-bold" style="font-size: 16px;">{{ strtoupper(substr(auth()->user()->username, 0, 2)) }}</span>
                @endif
            </div>
            <div>
                <h6 class="mb-0 fw-bold">{{ auth()->user()->username }}</h6>
            </div>
        </div>
    </div>
@endauth
<div class="m-2 card shadow bg-gray-500">
    <div class="card-body">
        <h5 class="card-title">Categories</h5>
        <div class="list-group">
            @include('includes.subcategories',['categories'=>$categories])
        </div>
    </div>
</div>


