@if ($paginator->hasPages())
    <ul class="flex space-x-3" role="navigation">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li>
                <button class="btn" type="button" disabled>
                    &lsaquo;
                </button>
            </li>
        @else
            <li>
                <button class="btn" type="button" wire:click="previousPage" rel="prev">
                    &lsaquo;
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
                        <li><button class="btn" type="button" disabled>{{ $page }}</button></li>
                    @else
                        <li><button class="btn" type="button" wire:click="gotoPage({{ $page }})">{{ $page }}</button></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li>
                <button class="btn" type="button" wire:click="nextPage" rel="next">
                    &rsaquo;
                </button>
            </li>
        @else
            <li>
                <button class="btn" type="button" disabled>
                    &rsaquo;
                </button>
            </li>
        @endif
    </ul>
@endif
