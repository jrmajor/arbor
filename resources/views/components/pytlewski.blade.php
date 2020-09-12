@if(! $id)
    {{ $slot }}
@else
    @php
        $person = App\Models\Person::findByPytlewskiId($id)
    @endphp
    @if (! $person)
        <a
            target="_blank" href="{{ App\Services\Pytlewski\Pytlewski::url($id) }}"
            class="text-red-600 hover:text-red-800">
            {{ $slot }}
        </a>
    @else
        <a
            href="{{ route('people.show', $person) }}"
            class="text-green-600 hover:text-green-800">
            {{ $slot }}
        </a>
    @endif
@endif
