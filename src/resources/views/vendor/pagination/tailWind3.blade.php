

@if ($paginator->hasPages())
    <ul class="pagination text-center">
        @if ($paginator->onFirstPage())
            <li class="page-item">
                <a class="page-link" aria-label="Previous">
                    <span aria-hidden="true">«</span><span class="sr-only">Previous</span>
                </a>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" aria-label="Previous">
                    <span aria-hidden="true">«</span><span class="sr-only">Previous</span>
                </a>
            </li>
        @endif
        @foreach ($elements as $element)
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active"><a class="page-link" href="#">{{ $page }}</a></li>
                    @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach
        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" aria-label="Next"><span aria-hidden="true">»</span>
                    <span class="sr-only">Next</span></a>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" aria-label="Next"><span aria-hidden="true">»</span>
                    <span class="sr-only">Next</span></a>
            </li>
        @endif
    </ul>
@endif