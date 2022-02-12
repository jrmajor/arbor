@if ($pytlewski->person)
  @can('view', $pytlewski->person)
    <a
      href="{{ route('people.show', $pytlewski->person) }}"
      class="text-green-600 hover:text-green-700"
    >
  @else
    <a
      target="_blank" href="{{ $pytlewski->url }}"
      class="text-yellow-600 hover:text-yellow-700"
    >
  @endcan
@elseif ($pytlewski->id)
  <a
    target="_blank" href="{{ $pytlewski->url }}"
    class="text-red-600 hover:text-red-700"
  >
@endif

  @if ($pytlewski->surname)
    {{ $pytlewski->surname }},
  @endif
  {{ $pytlewski->name }}{{--

--}}@if ($pytlewski->person || $pytlewski->id){{--
  --}}</a>{{--
--}}@endif
