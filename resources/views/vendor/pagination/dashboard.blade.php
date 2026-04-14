@if ($paginator->hasPages())
    <nav class="dashboard-pagination" role="navigation" aria-label="{{ __('pagination.aria_nav') }}">
        <div class="dashboard-pagination-mobile">
            @if ($paginator->onFirstPage())
                <span class="dashboard-pagination-nav dashboard-pagination-nav--disabled">{!! __('pagination.previous') !!}</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="dashboard-pagination-nav">{!! __('pagination.previous') !!}</a>
            @endif
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="dashboard-pagination-nav">{!! __('pagination.next') !!}</a>
            @else
                <span class="dashboard-pagination-nav dashboard-pagination-nav--disabled">{!! __('pagination.next') !!}</span>
            @endif
        </div>

        <div class="dashboard-pagination-desktop">
            <p class="dashboard-pagination-meta">
                @if ($paginator->firstItem())
                    {{ __('pagination.showing') }}
                    <span class="dashboard-pagination-strong">{{ $paginator->firstItem() }}</span>
                    {{ __('pagination.range_to') }}
                    <span class="dashboard-pagination-strong">{{ $paginator->lastItem() }}</span>
                    {{ __('pagination.of_total') }}
                    <span class="dashboard-pagination-strong">{{ $paginator->total() }}</span>
                    {{ __('pagination.results') }}
                @else
                    {{ __('pagination.summary_empty') }}
                @endif
            </p>
            <ul class="dashboard-pagination-list">
                @if ($paginator->onFirstPage())
                    <li class="dashboard-pagination-item">
                        <span class="dashboard-pagination-link dashboard-pagination-link--disabled" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">&lsaquo;</span>
                    </li>
                @else
                    <li class="dashboard-pagination-item">
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="dashboard-pagination-link" aria-label="{{ __('pagination.previous') }}">&lsaquo;</a>
                    </li>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li class="dashboard-pagination-item">
                            <span class="dashboard-pagination-link dashboard-pagination-link--ellipsis" aria-disabled="true">{{ $element }}</span>
                        </li>
                    @endif
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="dashboard-pagination-item">
                                    <span class="dashboard-pagination-link dashboard-pagination-link--current" aria-current="page">{{ $page }}</span>
                                </li>
                            @else
                                <li class="dashboard-pagination-item">
                                    <a href="{{ $url }}" class="dashboard-pagination-link">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <li class="dashboard-pagination-item">
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="dashboard-pagination-link" aria-label="{{ __('pagination.next') }}">&rsaquo;</a>
                    </li>
                @else
                    <li class="dashboard-pagination-item">
                        <span class="dashboard-pagination-link dashboard-pagination-link--disabled" aria-disabled="true" aria-label="{{ __('pagination.next') }}">&rsaquo;</span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif
