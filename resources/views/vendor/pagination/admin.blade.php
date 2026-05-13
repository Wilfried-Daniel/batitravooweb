{{-- Pagination admin — styles .admin-pagination (sans Tailwind) --}}
@php
    $total = method_exists($paginator, 'total') ? $paginator->total() : $paginator->count();
@endphp

@if ($paginator->hasPages())
    <div class="admin-pagination-wrap">
        <p class="admin-pagination__meta">
            @if ($paginator->firstItem())
                Affichage de <strong>{{ $paginator->firstItem() }}</strong> à <strong>{{ $paginator->lastItem() }}</strong>
                sur <strong>{{ $total }}</strong> résultat(s)
            @else
                {{ $total }} résultat(s)
            @endif
        </p>
        <nav class="admin-pagination" role="navigation" aria-label="Pagination">
            <ul class="admin-pagination__list">
                @if ($paginator->onFirstPage())
                    <li><span class="admin-pagination__item admin-pagination__item--disabled" aria-disabled="true">‹</span></li>
                @else
                    <li>
                        <a class="admin-pagination__item" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Page précédente">‹</a>
                    </li>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li><span class="admin-pagination__item admin-pagination__item--dots">{{ $element }}</span></li>
                    @endif
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li>
                                    <span class="admin-pagination__item admin-pagination__item--active" aria-current="page">{{ $page }}</span>
                                </li>
                            @else
                                <li><a class="admin-pagination__item" href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <li>
                        <a class="admin-pagination__item" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Page suivante">›</a>
                    </li>
                @else
                    <li><span class="admin-pagination__item admin-pagination__item--disabled" aria-disabled="true">›</span></li>
                @endif
            </ul>
        </nav>
    </div>
@elseif($total > 0)
    <p class="admin-pagination__meta admin-pagination__meta--solo">{{ $total }} résultat(s)</p>
@endif
