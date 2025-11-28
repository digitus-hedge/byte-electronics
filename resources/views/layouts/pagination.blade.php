<ul class="pagination pagination-sm m-0 float-right">
    @if ($paginator->onFirstPage())
        <li class="page-item disabled"><a class="page-link" href="#">«</a></li>
    @else
        <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}&part={{ $paginator->currentPage() - 1 }}">«</a></li>
    @endif

    @foreach ($elements as $element)
        <!-- "Three Dots" Separator -->
        @if (is_string($element))
            <li class="page-item"><a class="page-link" href="#">{{ $element }}</a></li>
        @endif

        <!-- Array Of Links -->
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <li class="page-item active"><a class="page-link" href="#">{{ $page }}</a></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $url }}&part={{ $page }}">{{ $page }}</a></li>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}&part={{ $paginator->currentPage() + 1 }}">»</a></li>
    @else
        <li class="page-item disabled"><a class="page-link" href="#">»</a></li>
    @endif
</ul>
{{-- 
<ul class="pagination pagination-rounded">
    <li class="page-item disabled">
        <span class="page-link"><i class="mdi mdi-chevron-left"></i></span>
    </li>
    <li class="page-item"><a class="page-link" href="#">1</a></li>
    <li class="page-item active">
        <span class="page-link">
            2
            <span class="sr-only">(current)</span>
        </span>
    </li>
    <li class="page-item"><a class="page-link" href="#">3</a></li>
    <li class="page-item">
        <a class="page-link" href="#"><i class="mdi mdi-chevron-right"></i></a>
    </li>
</ul> --}}
