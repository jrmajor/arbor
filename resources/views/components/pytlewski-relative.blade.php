@if ($pytlewski->person)
    <a
        href="{{ route('people.show', $pytlewski->person) }}"
        class="text-green-600 hover:text-green-800">
@elseif ($pytlewski->id)
    <a
        target="_blank" href="{{ $pytlewski->url }}"
        class="text-red-600 hover:text-red-800">
@endif

    @if($pytlewski->surname)
        {{ $pytlewski->surname }},
    @endif
    {{ $pytlewski->name }}

@if ($pytlewski->person || $pytlewski->id)
    </a>
@endif
