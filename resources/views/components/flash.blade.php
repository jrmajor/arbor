@if(flash()->message)
    <div
        class="w-full rounded-lg shadow p-5 flex items-center mb-3
        {{ match(flash()->level) {
            'error' => 'bg-red-50 text-red-900',
            'warning' => 'bg-yellow-50 text-yellow-900',
            'success' => 'bg-green-50 text-green-900',
        } }}">
        <svg class="flex-none w-5 h-5 mr-5 fill-current
            {{ match(flash()->level) {
                'error' => 'text-red-500',
                'warning' => 'text-yellow-500',
                'success' => 'text-green-500',
            } }}"
            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
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
