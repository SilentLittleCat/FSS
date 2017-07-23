@if ($paginator->hasPages())
    <ul class="pagination">
        @if ($paginator->onFirstPage())
            <li class="disabled"><span><i class="fa fa-fast-backward" aria-hidden="true"></i></span></li>
        @else
            <li><a href="{{ $paginator->url(1) }}" rel="prev"><i class="fa fa-fast-backward" aria-hidden="true"></i></a></li>
        @endif

        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="disabled"><span>{{ __('messages.page.previous') }}</span></li>
        @else
            <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">{{ __('messages.page.previous') }}</a></li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="disabled"><span>{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="active"><span>{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">{{ __('messages.page.next') }}</a></li>
        @else
            <li class="disabled"><span>{{ __('messages.page.next') }}</span></li>
        @endif

        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->url($paginator->lastPage()) }}" rel="next"><i class="fa fa-fast-forward" aria-hidden="true"></i></a></li>
        @else
            <li class="disabled"><span><i class="fa fa-fast-forward" aria-hidden="true"></i></span></li>
        @endif
        <li>
            <span class="pagination-input-span">
                <div class="input-group">
                    <input class="pagination-input" type="text" name="pagination-input" class="form-control" data-total="{{ $paginator->lastPage() }}">
                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-default pagination-input-btn">
                            {{ __('messages.go') }}
                        </button>
                    </div>
                </div>
            </span>
        </li>
    </ul>
@endif
