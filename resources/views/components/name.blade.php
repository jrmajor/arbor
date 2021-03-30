@if ($person->id_wielcy || $person->id_pytlewski)
  <div class="inline-block space-y-[0.139rem]">
    @if ($person->id_wielcy)
      <a class="block w-[0.417rem] h-[0.417rem] bg-[#73adff] border border-[#205daf]"
        href="{{ $person->wielcy->url }}" target="_blank"
      ></a>
    @else
      <span class="block w-[0.417rem] h-[0.417rem] bg-[#f5f8fc] border border-[#7ab8ff]"></span>
    @endif
    @if ($person->id_pytlewski)
      <a class="block w-[0.417rem] h-[0.417rem] bg-[#79d96a] border border-[#208712]"
        href="{{ $person->pytlewski->url }}" target="_blank"
      ></a>
    @else
      <span class="block w-[0.417rem] h-[0.417rem] bg-[#f5f8fc] border border-[#7ae16c]"></span>
    @endif
  </div>
@endif

@can('view', $person)
  <a href="{{ route('people.show', $person) }}" class="a">
@endcan

  @if ($person->dead)
    <i>
  @endif

    @can('view', $person)
      {{ $person->name }}

      @if ($person->last_name)
        @if (($bold ?? false) === 'l')<b>@endif
          {{ $person->last_name }}
        @if (($bold ?? false) === 'l')</b>@endif
        (@if (($bold ?? false) === 'f')<b>@endif{{ $person->family_name }}@if (($bold ?? false) === 'f')</b>@endif)
      @else
        @if ($bold ?? false)<b>@endif
          {{ $person->family_name }}
        @if ($bold ?? false)</b>@endif
      @endif

      @if ($years ?? true)
        @if ($person->birth_year && $person->death_year)
          (&#8727;&#xFE0E;{{ $person->birth_year }}, &#10013;&#xFE0E;{{ $person->death_year }})
        @elseif ($person->birth_year)
          (&#8727;&#xFE0E;{{ $person->birth_year }})
        @elseif ($person->death_year)
          (&#10013;&#xFE0E;{{ $person->death_year }})
        @endif
      @endif
    @else
      <small>[{{ __('misc.hidden') }}]</small>
    @endcan

  @if ($person->dead)
    </i>
  @endif

@can('view', $person)
  </a>
@endcan

@can('update', $person)
  <a
    href="{{ route('people.edit', $person) }}"
    class="a">
    <small>
      [№{{ $person->id }}]
    </small>
  </a>
@endcan
