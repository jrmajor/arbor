<div class="p-6 bg-white rounded-lg shadow">
  <dl>

    {{-- pytlewski --}}
    @if ($pytlewski = $person->pytlewski)
      <dt>{!! __('people.pytlewski.id') !!}&nbsp;</dt>
      <dd x-data="{ open: false }">
        <a href="{{ $pytlewski->url }}" target="_blank" class="a">
          {{ $pytlewski->id}}
          @if ($pytlewski->name || $pytlewski->family_name || $pytlewski->last_name)
            <small>
              {{ __('people.pytlewski.as') }}
              <strong>{{ $pytlewski->last_name
                        ? $pytlewski->last_name.' ('.$pytlewski->family_name.')'
                        : $pytlewski->family_name }}</strong>
              {{ $pytlewski->name }} {{ $pytlewski->middle_name }}
            </small>
          @endif
        </a>
        <button x-on:click="open = true"
          class="btn-out leading-none text-xs rounded px-2">
          {{ __('people.pytlewski.show_more') }}
        </button>
        <br>
        <div x-show="open" x-on:click.outside="open = false" style="display: none;">
          <small style="display: block; line-height: 1.45">
            @if ($pytlewski->mother || $pytlewski->father)
              &nbsp;&nbsp;{{ __('people.pytlewski.parents') }}:<br>
              @if ($pytlewski->mother)
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <x-pytlewski-relative :pytlewski="$pytlewski->mother"/>
                <br>
              @endif
              @if ($pytlewski->father)
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <x-pytlewski-relative :pytlewski="$pytlewski->father"/>
                <br>
              @endif
            @endif

            @if ($pytlewski->marriages->isNotEmpty())
              &nbsp;&nbsp;{{ __('people.pytlewski.marriages') }}:<br>
              @foreach ($pytlewski->marriages as $marriage)
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <x-pytlewski-relative :pytlewski="$marriage"/>
                <br>
              @endforeach
            @endif

            @if ($pytlewski->children->isNotEmpty())
              &nbsp;&nbsp;{{ __('people.pytlewski.children') }}:<br>
              @foreach ($pytlewski->children as $child)
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <x-pytlewski-relative :pytlewski="$child"/>
                <br>
              @endforeach
            @endif

            @if ($pytlewski->siblings->isNotEmpty())
              &nbsp;&nbsp;{{ __('people.pytlewski.siblings') }}:<br>
              @foreach ($pytlewski->siblings as $sibling)
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <x-pytlewski-relative :pytlewski="$sibling"/>
                <br>
              @endforeach
            @endif
          </small>
        </div>
      </dd>
    @endif

    {{-- wielcy --}}
    @if ($wielcy = $person->wielcy)
      <dt>{!! __('people.wielcy.id') !!}&nbsp;</dt>
      <dd>
        <a href="{{ $wielcy->url }}" target="_blank" class="a">
          {{ $wielcy->id }}
          @if ($wielcy->name)
            <small>{{ __('people.wielcy.as') }} {{ $wielcy->name }}</small>
          @endif
        </a>
      <br>
    @endif

    {{-- names --}}
    @if ($person->middle_name)
      <dt>{{ __('people.names') }}&nbsp;</dt>
      <dd>{{ $person->name }} {{ $person->middle_name }}</dd>
    @else
      <dt>{{ __('people.name') }}&nbsp;</dt>
      <dd>{{ $person->name }}</dd>
    @endif

    <dt>{{ __('people.family_name') }}&nbsp;</dt>
    <dd>{{ $person->family_name }}</dd>

    @if ($person->last_name)
      <dt>{{ __('people.last_name') }}&nbsp;</dt>
      <dd>{{ $person->last_name }}</dd>
    @endif

    {{-- birth --}}
    @if ($person->birth_date || $person->birth_place  || $person->estimatedBirthDate())
      <dt>{{ __('people.birth') }}&nbsp;</dt>
      <dd>
        @php $some_birth_data_printed = false; @endphp
        @if ($person->birth_date)
          {{ $person->birth_date }}<br>
          @php $some_birth_data_printed = true; @endphp
        @endif
        @if ($person->birth_place)
          @if ($some_birth_data_printed)
            &nbsp;&nbsp;
          @endif
          {{ $person->birth_place }}<br>
          @php $some_birth_data_printed = true; @endphp
        @endif
        @if (! $person->dead && $person->currentAge() !== null)
          @if ($some_birth_data_printed)
            &nbsp;&nbsp;
          @endif
          {{ __('people.current_age') }}:
          {{ __('misc.year', ['rawAge' => $person->currentAge(true), 'age' => $person->currentAge()]) }}
          <br>
          @php $some_birth_data_printed = true; @endphp
        @endif
        @if (
          (! $person->birth_date || auth()->user()?->isSuperAdmin())
          && $person->estimatedBirthDate()
        )
          @if ($some_birth_data_printed === true)
            &nbsp;&nbsp;
          @endif
          {{ __('people.estimated_birth_date') }}: {!! $person->estimatedBirthDate() !!}
          @if ($person->estimatedBirthDateError())
            <small>
              (<strong>{{ $person->estimatedBirthDateError() }}</strong>
              {{ __('misc.years_of_error', ['age' => $person->estimatedBirthDateError()]) }})
            </small>
          @endif
        @endif
      </dd>
    @endif

    {{-- death --}}
    @if ($person->dead)
      <dt>{{ __('people.death') }}&nbsp;</dt>
      @if ($person->death_date || $person->death_place || $person->death_cause)
        <dd>
          @php $some_death_data_printed = false; @endphp
          @if ($person->death_date)
            {{ $person->death_date }}<br>
            @php $some_death_data_printed = true; @endphp
          @endif
          @if ($person->death_place)
            @if ($some_death_data_printed)
              &nbsp;&nbsp;
            @endif
            {{ $person->death_place }}<br>
            @php $some_death_data_printed = true; @endphp
          @endif
          @if ($person->death_cause)
            @if ($some_death_data_printed)
              &nbsp;&nbsp;
            @endif
            {{ $person->death_cause }}<br>
            @php $some_death_data_printed = true; @endphp
          @endif
          @if ($person->ageAtDeath() !== null)
            @if ($some_death_data_printed)
              &nbsp;&nbsp;
            @endif
            {{ __('people.death_age') }}:
            {{ __('misc.year', ['rawAge' => $person->ageAtDeath(true), 'age' => $person->ageAtDeath()]) }}
          @endif
        </dd>
      @else
        <dd>&#10013;&#xFE0E;</dd>
      @endif
    @endif

    {{-- funeral --}}
    @if ($person->funeral_date || $person->funeral_place)
      @if ($person->funeral_date && ! $person->funeral_place)
        <dt>{{ __('people.funeral') }}&nbsp;</dt>
        <dd>{{ $person->funeral_date }}</dd>
      @elseif ($person->funeral_place && ! $person->funeral_date)
        <dt>{{ __('people.funeral') }}&nbsp;</dt>
        <dd>{{ $person->funeral_place }}</dd>
      @elseif ($person->funeral_place && $person->funeral_date)
        <dt>{{ __('people.funeral') }}&nbsp;</dt>
        <dd>{{ $person->funeral_date }}<br/>&nbsp;&nbsp;{{ $person->funeral_place }}</dd>
      @endif
    @endif

    {{-- burial --}}
    @if ($person->burial_date || $person->burial_place)
      @if ($person->burial_date && ! $person->burial_place)
        <dt>{{ __('people.burial') }}&nbsp;</dt>
        <dd>{{ $person->burial_date }}</dd>
      @elseif ($person->burial_place && ! $person->burial_date)
        <dt>{{ __('people.burial') }}&nbsp;</dt>
        <dd>{{ $person->burial_place }}</dd>
      @elseif ($person->burial_place && $person->burial_date)
        <dt>{{ __('people.burial') }}&nbsp;</dt>
        <dd>{{ $person->burial_date }}<br/>&nbsp;&nbsp;{{ $person->burial_place }}</dd>
      @endif
    @endif

    {{-- parents --}}
    @if ($person->mother)
      <dt>{{ __('people.mother') }}&nbsp;</dt>
      <dd><x-name :person="$person->mother"/></dd>
    @endif
    @if ($person->father)
      <dt>{{ __('people.father') }}&nbsp;</dt>
      <dd><x-name :person="$person->father"/></dd>
    @endif

    {{-- siblings --}}
    @if ($person->siblings->isNotEmpty())
      <dt>{{ __('people.siblings') }} ({{ $person->siblings->count() }})&nbsp;</dt>
      <dd>
        <ul>
          @foreach ($person->siblings as $sibling)
            <li><x-name :person="$sibling"/></li>
          @endforeach
        </ul>
      </dd>
    @endif

    {{-- przyr. od str. matki --}}
    @if ($person->siblings_mother->isNotEmpty())
      <dt>{{ __('people.siblings_mother') }} ({{ $person->siblings_mother->count() }})&nbsp;</dt>
      <dd>
        <ul>
          @foreach ($person->siblings_mother as $sibling)
            <li><x-name :person="$sibling"/></li>
          @endforeach
        </ul>
      </dd>
    @endif

    {{-- przyr. od str. ojca --}}
    @if ($person->siblings_father->isNotEmpty())
      <dt>{{ __('people.siblings_father') }} ({{ $person->siblings_father->count() }})&nbsp;</dt>
      <dd>
        <ul>
          @foreach ($person->siblings_father as $sibling)
            <li><x-name :person="$sibling"/></li>
          @endforeach
        </ul>
      </dd>
    @endif

    {{-- marriages --}}
    @if ($person->marriages->isNotEmpty())
      <dt>{{ __('people.marriages') }}&nbsp;</dt>
      <dd>
        <ul>
          @foreach ($person->marriages as $marriage)
            @can ('view', $marriage)
              <li>
                @if ($person->marriages->count() > 1 && $marriage->order($person))
                  {{ strtolower(roman($marriage->order($person))) }}.
                @endif
                @if ($marriage->rite)
                  <strong>{{ __('marriages.rites.' . $marriage->rite) }}:</strong>
                @endif

                <x-name :person="$marriage->partner($person)"/>

                @can('update', $marriage)
                  <a
                    href="{{ route('marriages.edit', ['marriage' => $marriage]) }}"
                    class="a">
                    <small>[{{ __('marriages.marriage') }} №{{ $marriage->id }}]</small>
                  </a>
                  <a href="{{ route('people.create', [
                      'mother' => $marriage->woman_id,
                      'father' => $marriage->man_id,
                    ]) }}"
                    class="a">
                    <small>[+]</small>
                  </a>
                @endcan
                @if ($marriage->hasFirstEvent())
                  @if ($marriage->first_event_type)
                    <br>&nbsp;&nbsp;{{ __('marriages.event_types.'.$marriage->first_event_type) }}:
                  @else
                    <br>&nbsp;
                  @endif
                  @if ($marriage->first_event_place && $marriage->first_event_date)
                    {{ $marriage->first_event_place }}, {{ $marriage->first_event_date }}
                  @elseif ($marriage->first_event_place)
                    {{ $marriage->first_event_place }}
                  @elseif ($marriage->first_event_date)
                    {{ $marriage->first_event_date }}
                  @endif
                @endif
                @if ($marriage->hasSecondEvent())
                  @if ($marriage->second_event_type)
                    <br>&nbsp;&nbsp;{{ __('marriages.event_types.'.$marriage->second_event_type) }}:
                  @else
                    <br>&nbsp;
                  @endif
                  @if ($marriage->second_event_place && $marriage->second_event_date)
                    {{ $marriage->second_event_place }}, {{ $marriage->second_event_date }}
                  @elseif ($marriage->second_event_place)
                    {{ $marriage->second_event_place }}
                  @elseif ($marriage->second_event_date)
                    {{ $marriage->second_event_date }}
                  @endif
                @endif

                @if ($marriage->divorced)
                  @if ($marriage->divorce_place && $marriage->divorce_date)
                    <br>&nbsp;&nbsp;{{ strtolower(__('marriages.divorced')) }}: {{ $marriage->divorce_place }}, {{ $marriage->divorce_date }}
                  @elseif ($marriage->divorce_place)
                    <br>&nbsp;&nbsp;{{ strtolower(__('marriages.divorced')) }}: {{ $marriage->divorce_place }}
                  @elseif ($marriage->divorce_date)
                    <br>&nbsp;&nbsp;{{ strtolower(__('marriages.divorced')) }}: {{ $marriage->divorce_date }}
                  @else
                    <br>&nbsp;&nbsp;{{ strtolower(__('marriages.divorced')) }}
                  @endif
                @endif
              </li>
            @else
              <li>
                @if ($marriage->order($person))
                  {{ strtolower(roman($marriage->order($person))) }}.
                @endif

                <small>[{{ __('misc.hidden') }}]</small>
                <small>[{{ __('marriages.marriage') }} №{{ $marriage->id }}]</small>
              </li>
            @endcan
          @endforeach
        </ul>
      </dd>
    @endif

    {{-- children --}}
    @if ($person->children->isNotEmpty())
      <dt>{{ __('people.children') }} ({{ $person->children->count() }})&nbsp;</dt>
      <dd>
        <ul>
          @foreach ($person->children as $child)
            <li><x-name :person="$child"/></li>
          @endforeach
        </ul>
      </dd>
    @endif

    {{-- sources --}}
    @if ($person->sources->isNotEmpty())
      <dt>{{ __('people.sources') }}&nbsp;</dt>
      <dd>
        <ul>
          @foreach ($person->sources as $source)
            <li><small class="text-black">{!! $source->markup() !!}</small></li>
          @endforeach
        </ul>
      </dd>
    @endif

    {{-- notes --}}
    {{--
    <dt>
      Notes
      <a href="" data-toggle="tooltip" data-html="true" title="show_notes_versions">
        <small>[total 234_version]</small>
      </a>&nbsp;
      <a href="" data-toggle="tooltip" title="edit_notes" target="_blank">
        <small>[+]</small>
      </a>&nbsp;
    </dt>
    <dd>
      notes<br>
      note_deleted_you_can_see <a href="">earlier_versions</a> or <a href="">add_new_one</a>.
    </dd>
    <dt>Notes <small>[no_notes]</small>&nbsp</dt>
    <dd>
      <a href="" data-toggle="tooltip" data-html="true" title="click_to_create_note" target="_blank">[+]</a>
    </dd>
    --}}

  </dl>
</div>
