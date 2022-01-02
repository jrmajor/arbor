@php

use App\Services\Pytlewski\Pytlewski;

@endphp

<div class="p-6 bg-white rounded-lg shadow">
  <dl>

    {{-- pytlewski --}}
    @if ($person->id_pytlewski && ! $person->pytlewski)
      <dt>{!! __('people.pytlewski.id') !!}</dt>
      <dd>
        <a href="{{ Pytlewski::url($person->id_pytlewski) }}" target="_blank" class="a">
          {{ $person->id_pytlewski }}
        </a>
      </dd>
    @elseif ($pytlewski = $person->pytlewski)
      <dt>{!! __('people.pytlewski.id') !!}</dt>
      <dd x-data="{ open: false }">
        <a href="{{ $pytlewski->url }}" target="_blank" class="a">
          {{ $pytlewski->id}}
          <small>
            {{ __('people.pytlewski.as') }}
            <strong>{{ $pytlewski->last_name
                      ? $pytlewski->last_name.' ('.$pytlewski->family_name.')'
                      : $pytlewski->family_name }}</strong>
            {{ $pytlewski->name }} {{ $pytlewski->middle_name }}
          </small>
        </a>
        <button x-on:click="open = true"
          class="btn-out leading-none text-xs rounded px-2">
          {{ __('people.pytlewski.show_more') }}
        </button>
        <small
          x-show="open"
          x-on:click.outside="open = false"
          style="display: none;"
          class="block -mt-0.5 ml-4 leading-tight"
        >
          @if ($pytlewski->mother || $pytlewski->father)
            <p class="mt-1.5">{{ __('people.pytlewski.parents') }}:</p>
            @if ($pytlewski->mother)
              <p class="ml-4"><x-pytlewski-relative :pytlewski="$pytlewski->mother"/></p>
            @endif
            @if ($pytlewski->father)
              <p class="ml-4"><x-pytlewski-relative :pytlewski="$pytlewski->father"/></p>
            @endif
          @endif

          @if ($pytlewski->marriages->isNotEmpty())
            <p class="mt-1.5">{{ __('people.pytlewski.marriages') }}:</p>
            @foreach ($pytlewski->marriages as $marriage)
              <p class="ml-4"><x-pytlewski-relative :pytlewski="$marriage"/></p>
            @endforeach
          @endif

          @if ($pytlewski->children->isNotEmpty())
            <p class="mt-1.5">{{ __('people.pytlewski.children') }}:</p>
            <p class="ml-4">
              @foreach ($pytlewski->children as $child)
                <x-pytlewski-relative :pytlewski="$child"/>{{ $loop->last ? '' : ', ' }}
              @endforeach
            </p>
          @endif

          @if ($pytlewski->siblings->isNotEmpty())
            <p class="mt-1.5">{{ __('people.pytlewski.siblings') }}:</p>
            <p class="ml-4">
              @foreach ($pytlewski->siblings as $sibling)
                <x-pytlewski-relative :pytlewski="$sibling"/>{{ $loop->last ? '' : ', ' }}
              @endforeach
            </p>
          @endif
        </small>
      </dd>
    @endif

    {{-- wielcy --}}
    @if ($wielcy = $person->wielcy)
      <dt>{!! __('people.wielcy.id') !!}</dt>
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
      <dt>{{ __('people.names') }}</dt>
      <dd>{{ $person->name }} {{ $person->middle_name }}</dd>
    @else
      <dt>{{ __('people.name') }}</dt>
      <dd>{{ $person->name }}</dd>
    @endif

    <dt>{{ __('people.family_name') }}</dt>
    <dd>{{ $person->family_name }}</dd>

    @if ($person->last_name)
      <dt>{{ __('people.last_name') }}</dt>
      <dd>{{ $person->last_name }}</dd>
    @endif

    {{-- birth --}}
    @if ($person->birth_date || $person->birth_place  || $person->age->estimatedBirthDate())
      <dt>{{ __('people.birth') }}</dt>
      <dd class="indent-children-except-first">
        <x-optional-date-place :date="$person->birth_date" :place="$person->birth_place" multiline/>
        @if (! $person->dead && $person->age->prettyCurrent() !== null)
          <p>
            {{ __('people.current_age') }}:
            {{ __('misc.year', ['rawAge' => $person->age->current(), 'age' => $person->age->prettyCurrent()]) }}
          </p>
        @endif
        @if (
          (! $person->birth_date || auth()->user()?->isSuperAdmin())
          && $person->age->estimatedBirthDate() !== null
        )
          <p>
            {{ __('people.estimated_birth_date') }}: {!! $person->age->estimatedBirthDate() !!}
            @if ($person->age->estimatedBirthDateError() !== null)
              <small>
                (<strong>{{ $person->age->estimatedBirthDateError() }}</strong>
                {{ __('misc.years_of_error', ['age' => $person->age->estimatedBirthDateError()]) }})
              </small>
            @endif
          </p>
        @endif
      </dd>
    @endif

    {{-- death --}}
    @if ($person->dead)
      <dt>{{ __('people.death') }}</dt>
      @if ($person->death_date || $person->death_place || $person->death_cause)
        <dd class="indent-children-except-first">
          <x-optional-date-place :date="$person->death_date" :place="$person->death_place" multiline/>
          @if ($person->death_cause)
            <p>{{ $person->death_cause }}</p>
          @endif
          @if ($person->age->prettyAtDeath() !== null)
            <p>
              {{ __('people.death_age') }}:
              {{ __('misc.year', ['rawAge' => $person->age->atDeath(), 'age' => $person->age->prettyAtDeath()]) }}
            </p>
          @endif
        </dd>
      @else
        <dd>&#10013;&#xFE0E;</dd>
      @endif
    @endif

    {{-- funeral --}}
    @if ($person->funeral_date || $person->funeral_place)
      <dt>{{ __('people.funeral') }}</dt>
      <dd class="indent-children-except-first">
        <x-optional-date-place :date="$person->funeral_date" :place="$person->funeral_place" multiline/>
      </dd>
    @endif

    {{-- burial --}}
    @if ($person->burial_date || $person->burial_place)
      <dt>{{ __('people.burial') }}</dt>
      <dd class="indent-children-except-first">
        <x-optional-date-place :date="$person->burial_date" :place="$person->burial_place" multiline/>
      </dd>
    @endif

    {{-- parents --}}
    @if ($person->mother)
      <dt>{{ __('people.mother') }}</dt>
      <dd><x-name :person="$person->mother"/></dd>
    @endif
    @if ($person->father)
      <dt>{{ __('people.father') }}</dt>
      <dd><x-name :person="$person->father"/></dd>
    @endif

    {{-- siblings --}}
    @if ($person->siblings->isNotEmpty())
      <dt>{{ __('people.siblings') }} ({{ $person->siblings->count() }})</dt>
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
      <dt>{{ __('people.siblings_mother') }} ({{ $person->siblings_mother->count() }})</dt>
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
      <dt>{{ __('people.siblings_father') }} ({{ $person->siblings_father->count() }})</dt>
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
      <dt>{{ __('people.marriages') }}</dt>
      <dd>
        <ul>
          @foreach ($person->marriages as $marriage)
            @can ('view', $marriage)
              <li class="indent-children-except-first">
                @if ($person->marriages->count() > 1 && $marriage->order($person))
                  {{ strtolower(roman($marriage->order($person))) }}.
                @endif
                @if ($marriage->rite)
                  <strong>{{ __('marriages.rites.' . $marriage->rite->value) }}:</strong>
                @endif

                <x-name :person="$marriage->partner($person)"/>

                @can('update', $marriage)
                  <a href="{{ route('marriages.edit', ['marriage' => $marriage]) }}" class="a">
                    <small>[{{ __('marriages.marriage') }} №{{ $marriage->id }}]</small>
                  </a>
                  <a href="{{ route('people.create', [
                      'mother' => $marriage->woman_id,
                      'father' => $marriage->man_id]) }}"
                    class="a"
                  >
                    <small>[+]</small>
                  </a>
                @endcan
                @if ($marriage->hasFirstEvent())
                  <x-optional-date-place :date="$marriage->first_event_date" :place="$marriage->first_event_place">
                    @if ($marriage->first_event_type)
                      {{ __('marriages.event_types.' . $marriage->first_event_type->value) }}
                    @endif
                  </x-optional-date-place>
                @endif
                @if ($marriage->hasSecondEvent())
                  <x-optional-date-place :date="$marriage->second_event_date" :place="$marriage->second_event_place">
                    @if ($marriage->second_event_type)
                      {{ __('marriages.event_types.' . $marriage->second_event_type->value) }}
                    @endif
                  </x-optional-date-place>
                @endif

                @if ($marriage->divorced)
                  <x-optional-date-place :date="$marriage->divorce_date" :place="$marriage->divorce_place">
                    {{ strtolower(__('marriages.divorced')) }}
                  </x-optional-date-place>
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
      <dt>{{ __('people.children') }} ({{ $person->children->count() }})</dt>
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
      <dt>{{ __('people.sources') }}</dt>
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
    <dt>Notes <small>[no_notes]</small></dt>
    <dd>
      <a href="" data-toggle="tooltip" data-html="true" title="click_to_create_note" target="_blank">[+]</a>
    </dd>
    --}}

  </dl>
</div>
