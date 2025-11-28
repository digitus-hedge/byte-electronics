<ul class="list-group mt-2">
    @foreach($subcategories as $subcategory)
        <li class="list-group-item">
            {{ $subcategory->name }}
            @if($subcategory->children->isNotEmpty())
                @include('categories.subcategories', ['subcategories' => $subcategory->children])
            @endif
        </li>
    @endforeach
</ul>
