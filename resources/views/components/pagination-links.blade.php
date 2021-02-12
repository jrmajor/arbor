@if($paginator->hasPages())
    <ul class="flex justify-center lg:justify-start flex-wrap space-x-3 -my-1" role="navigation">

        @if($paginator->onFirstPage())
            <li class="py-1">
                <button id="pg-previous-disabled" class="btn-out" type="button" disabled>
                    &lsaquo;
                </button>
            </li>
        @else
            <li class="py-1">
                <button id="pg-previous" class="btn-out" type="button" wire:click="previousPage" rel="prev">
                    &lsaquo;
                </button>
            </li>
        @endif

        @foreach($elements as $element)

            @if(is_string($element))
                <li class="py-1"><span class="-m-1">{{ $element }}</span></li>
            @endif

            @if(is_array($element))
                @foreach($element as $page => $url)
                    @if($page === $paginator->currentPage())
                        <li class="py-1"><button id="pg-current" class="btn-out" type="button" disabled>{{ $page }}</button></li>
                    @else
                        <li class="py-1"><button id="pg-{{ $page }}" class="btn-out" type="button" wire:click="gotoPage({{ $page }})">{{ $page }}</button></li>
                    @endif
                @endforeach
            @endif

        @endforeach

        @if($paginator->hasMorePages())
            <li class="py-1">
                <button id="pg-next" class="btn-out" type="button" wire:click="nextPage" rel="next">
                    &rsaquo;
                </button>
            </li>
        @else
            <li class="py-1">
                <button id="pg-next-disabled" class="btn-out" type="button" disabled>
                    &rsaquo;
                </button>
            </li>
        @endif
    </ul>
@endif
