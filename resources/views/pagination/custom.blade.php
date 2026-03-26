@if ($paginator->hasPages())
    <div class="toolbox toolbox-pagination justify-content-between">
        <p class="showing-info mb-2 mb-sm-0">
            Showing 
            <span>
                {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} of {{ $paginator->total() }}
            </span>
            Products
        </p>

        <ul class="pagination">
            @if ($paginator->onFirstPage())
                <li class="prev disabled">
                    <a href="#" aria-label="Previous" aria-disabled="true">
                        <i class="w-icon-long-arrow-left"></i> Prev
                    </a>
                </li>
            @else
                <li class="prev">
                    <a href="{{ $paginator->previousPageUrl() }}" aria-label="Previous">
                        <i class="w-icon-long-arrow-left"></i> Prev
                    </a>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="disabled"><a href="#">{{ $element }}</a></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <a class="page-link" href="#">{{ $page }}</a>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li class="next">
                    <a href="{{ $paginator->nextPageUrl() }}" aria-label="Next">
                        Next <i class="w-icon-long-arrow-right"></i>
                    </a>
                </li>
            @else
                <li class="next disabled">
                    <a href="#" aria-label="Next" aria-disabled="true">
                        Next <i class="w-icon-long-arrow-right"></i>
                    </a>
                </li>
            @endif
        </ul>
    </div>
@endif
