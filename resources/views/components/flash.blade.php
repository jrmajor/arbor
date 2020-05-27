@if (flash()->message)
    <div
        class="w-full rounded-lg shadow-md p-3 flex items-center mb-3
        @if(flash()->level === 'error')
            bg-red-100 text-red-900
        @elseif(flash()->level === 'warning')
            bg-yellow-100 text-yellow-900
        @elseif(flash()->level === 'success')
            bg-green-100 text-green-900
        @endif ">
        <svg class="flex-none w-5 h-5 mr-3 fill-current
            @if(flash()->level === 'error')
                text-red-500
            @elseif(flash()->level === 'warning')
                text-yellow-500
            @elseif(flash()->level === 'success')
                text-green-500
            @endif " xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            @if(flash()->level === 'error')
                <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zM11.4 10l2.83-2.83-1.41-1.41L10 8.59 7.17 5.76 5.76 7.17 8.59 10l-2.83 2.83 1.41 1.41L10 11.41l2.83 2.83 1.41-1.41L11.41 10z"/> --}}
            @elseif(flash()->level === 'warning')
                <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zM9 5v6h2V5H9zm0 8v2h2v-2H9z"/>
            @elseif(flash()->level === 'success')
                <path d="M0 11l2-2 5 5L18 3l2 2L7 18z"/>
            @endif
        </svg>
        {{ flash()->message }}
    </div>
@endif
