<ul class="dropdown-menu list-group-flush rounded-bottom rounded mt-5"
    style="min-width: 350px;">
    @foreach($categories as $category)
        <li class="dropdown-item-group">
            <div class="d-flex align-items-center justify-content-between py-3 px-3 hover-item hover:bg-yellow-500 rounded m-1">
                <a href="{{ route('category.show', $category) }}" class="text-decoration-none text-dark">
                    {{ $category->name }}
                </a>
                @if($category->children->isNotEmpty())
                    <span class="badge text-bg-secondary">{{ $category -> num_products }}</span>
                    <i class="fas fa-chevron-right ms-2"></i>
                @endif
            </div>
            @if($category->children->isNotEmpty())
                <ul class="dropdown-menu dropdown-submenu rounded-3">
                    @foreach($category->children as $child)
                        <li>
                            <a class="dropdown-item py-3 hover:bg-yellow-500 rounded" href="{{ route('category.show', $child) }}">
                                {{ $child->name }}
                                <span class="badge text-bg-secondary">{{ $child -> num_products }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
</ul>
