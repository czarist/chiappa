@if ($paginator->hasPages())
<nav class="admin-pagination" aria-label="Paginação">
    @if ($paginator->onFirstPage())
        <span class="is-disabled"><i class="fas fa-chevron-left"></i></span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev"><i class="fas fa-chevron-left"></i></a>
    @endif

    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="is-disabled">{{ $element }}</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="is-current">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next"><i class="fas fa-chevron-right"></i></a>
    @else
        <span class="is-disabled"><i class="fas fa-chevron-right"></i></span>
    @endif
</nav>
@endif
