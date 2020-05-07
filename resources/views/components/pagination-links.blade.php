@if ($paginator->hasPages())
    <ul class="flex" role="navigation">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li>
                <button class="mx-1" type="button" disabled>
                    <span>&lsaquo;</span>
                    <span>{{ __('pagination.previous') }}</span>
                </button>
            </li>
        @else
            <li>
                <button class="mx-1" type="button" wire:click="previousPage" rel="prev">
                    <span>&lsaquo;</span>
                    <span>{{ __('pagination.previous') }}</span>
                </button>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li><span>{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li><button class="mx-1" type="button" disabled>{{ $page }}</button></li>
                    @else
                        <li><button class="mx-1" type="button" wire:click="gotoPage({{ $page }})">{{ $page }}</button></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li>
                <button class="mx-1" type="button" wire:click="nextPage" rel="next">
                    <span>{{ __('pagination.next') }}</span>
                    <span>&rsaquo;</span>
                </button>
            </li>
        @else
            <li>
                <button class="mx-1" type="button" disabled>
                    <span>{{ __('pagination.next') }}</span>
                    <span>&rsaquo;</span>
                </button>
            </li>
        @endif
    </ul>
@endif
