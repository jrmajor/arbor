@extends('layouts.app')

@section('content')
    <h3>
        @if($person->sex == 'xx')
            &#9792;
        @elseif($person->sex == 'xy')
            &#9794;
        @endif

        @if($person->dead)
            <i>
        @endif

            @if($person->canBeViewedBy(auth()->user()))
                {{ $person->name }}
                @if($person->last_name)
                    {{ $person->last_name }} (z d. {{ $person->family_name }})
                @else
                    {{ $person->family_name }}
                @endif
            @else
                [{{ __('misc.hidden') }}]
            @endif

        @if($person->dead)
            </i>
        @endif

        @if(optional(auth()->user())->canWrite())
            <a href="{{ route('people.edit', ['person' => $person->id]) }}"
                data-tippy-content="{{ __('people.edit_this_person') }}">
                <small class="text-lg">
                    [№{{ $person->id }}]
                </small>
            </a>
            <a href="{{ route('marriages.create') }}?{{ $person->sex == 'xx' ? 'woman' : 'man' }}={{ $person->id }}">
                <small class="text-lg">
                    [{{ strtolower(__('marriages.add_a_new_marriage')) }}]
                </small>
            </a>
        @else
            <small class="text-lg">[№{{ $person->id }}]</small>
        @endif
    </h3>

    <dl class="mb-3">
        {{-- pytlewski --}}
        @if($pytlewski = $person->pytlewski)
            <dt>@lang('people.pytlewski.id')&nbsp;</dt>
            <dd x-data="{ open: false }">
                <a href="{{ $pytlewski->url }}" target="_blank">
                    {{ $pytlewski->id}}
                    @if($pytlewski->basic_name)
                        <small>
                            {{ __('people.pytlewski.as') }}
                            {!! $pytlewski->basic_name !!}
                        </small>
                    @endif
                </a>
                <button @click="open = true"
                    class="leading-none text-xs px-2 py-1 normal-case font-normal tracking-normal">
                    {{ __('people.pytlewski.show_more') }}
                </button>
                <br>
                <div x-show="open" @click.away="open = false" style="display: none;">
                    <small style="display: block; line-height: 1.45">
                        @if($pytlewski->parents)
                            &nbsp;&nbsp;{{ __('people.pytlewski.parents') }}: <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            @component('components.pytlewski', ['id' => $pytlewski->mother_id])
                                {{ $pytlewski->mother_surname }}, {{ $pytlewski->mother_name }}
                            @endcomponent
                            <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            @component('components.pytlewski', ['id' => $pytlewski->father_id])
                                {{ $pytlewski->father_surname }}, {{ $pytlewski->father_name }}
                            @endcomponent
                            <br>
                        @endif

                        @if($pytlewski->marriages || $pytlewski->total_marriages != 0)
                            &nbsp;&nbsp;{{ __('people.pytlewski.marriages') }}: {{ $pytlewski->total_marriages }}<br>
                            @foreach($pytlewski->marriages as $key => $val)
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                @component('components.pytlewski', ['id' => isset($val['id']) ? $val['id'] : null])
                                    {{ $val['name'] }}
                                @endcomponent
                                <br>
                            @endforeach
                        @endif

                        @if($pytlewski->children || $pytlewski->total_children != 0)
                            &nbsp;&nbsp;{{ __('people.pytlewski.children') }}: {{ $pytlewski->total_children }}<br>
                            @foreach($pytlewski->children as $key => $val)
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                @component('components.pytlewski', ['id' => isset($val['id']) ? $val['id'] : null])
                                    {{ $val['name'] }}
                                @endcomponent
                                <br>
                            @endforeach
                        @endif

                        @if($pytlewski->siblings || $pytlewski->total_siblings != 0)
                            &nbsp;&nbsp;{{ __('people.pytlewski.siblings') }}: {{ $pytlewski->total_siblings }}<br>
                            @foreach($pytlewski->siblings as $key => $val)
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                @component('components.pytlewski', ['id' => isset($val['id']) ? $val['id'] : null])
                                    {{ $val['name'] }}
                                @endcomponent
                                <br>
                            @endforeach
                        @endif
                    </small>
                </div>
            </dd>
        @endif

        {{-- wielcy --}}
        @if($wielcy = $person->wielcy)
            <dt>@lang('people.wielcy.id')&nbsp;</dt>
            <dd>
                <a href="{{ $wielcy->url }}" target="_blank">
                    {{ $wielcy->id }}
                    <small>{{ __('people.wielcy.as') }} {!! $wielcy->name !!}</small>
                </a>
            <br>
        @endif

        {{-- names --}}
        @if($person->middle_name)
            <dt>{{ __('people.names') }}&nbsp;</dt>
            <dd>
                @if($person->canBeViewedBy(auth()->user()))
                    {{ $person->name }} {{ $person->middle_name }}
                @else
                    <small>[{{ __('misc.hidden') }}]</small>
                @endif
            </dd>
        @else
            <dt>{{ __('people.name') }}&nbsp;</dt>
            <dd>
                @if($person->canBeViewedBy(auth()->user()))
                    {{ $person->name }}
                @else
                    <small>[{{ __('misc.hidden') }}]</small>
                @endif
            </dd>
        @endif
        <dt>{{ __('people.family_name') }}&nbsp;</dt>
        <dd>
            @if($person->canBeViewedBy(auth()->user()))
                {{ $person->family_name }}
            @else
                <small>[{{ __('misc.hidden') }}]</small>
            @endif
        </dd>
        @if($person->last_name)
            <dt>{{ __('people.last_name') }}&nbsp;</dt>
            <dd>
                @if($person->canBeViewedBy(auth()->user()))
                    {{ $person->last_name }}
                @else
                    <small>[{{ __('misc.hidden') }}]</small>
                @endif
            </dd>
        @endif

        {{-- birth --}}
        @if($person->birth_date || $person->birth_place  || $person->estimatedBirthDate())
            @if($person->canBeViewedBy(auth()->user()))
                <dt>{{ __('people.birth') }}&nbsp;</dt>
                <dd>
                    @php $some_birth_data_printed = false; @endphp
                    @if($person->birth_date)
                        {{ $person->birth_date }}<br>
                        @php $some_birth_data_printed = true; @endphp
                    @endif
                    @if($person->birth_place)
                        @if($some_birth_data_printed)
                            &nbsp;&nbsp;
                        @endif
                        {{ $person->birth_place }}<br>
                        @php $some_birth_data_printed = true; @endphp
                    @endif
                    @if(! $person->dead && $person->currentAge())
                        @if($some_birth_data_printed)
                            &nbsp;&nbsp;
                        @endif
                        {{ __('people.current_age') }}: {!! trans_choice('misc.year', $person->currentAge('now')) !!}
                        <br>
                        @php $some_birth_data_printed = true; @endphp
                    @endif
                    @if(
                        (! $person->birth_date || optional(auth()->user())->isSuperAdmin())
                        && $person->estimatedBirthDateError()
                    )
                        @if($some_birth_data_printed == true)
                            &nbsp;&nbsp;
                        @endif
                        {{ __('people.estimated_birth_date') }}: {!! $person->estimatedBirthDate() !!}
                        @if($person->estimatedBirthDateError())
                            <small>
                                (<strong>{{ $person->estimatedBirthDateError() }}</strong>
                                {{ __('people.years_of_error') }})
                            </small>
                        @endif
                    @endif
                </dd>
            @else
                <dt>{{ __('people.birth') }}&nbsp;</dt>
                <dd>
                   <small>[{{ __('misc.hidden') }}]</small>
                </dd>
            @endif
        @endif

        {{-- death --}}
        @if($person->dead)
            <dt>{{ __('people.death') }}&nbsp;</dt>
            @if($person->death_date || $person->death_place || $person->death_cause)
                <dd>
                    @php $some_death_data_printed = false; @endphp
                    @if($person->death_date)
                        {{ $person->death_date }}<br>
                        @php $some_death_data_printed = true; @endphp
                    @endif
                    @if($person->death_place)
                        @if($some_death_data_printed)
                            &nbsp;&nbsp;
                        @endif
                        {{ $person->death_place }}<br>
                        @php $some_death_data_printed = true; @endphp
                    @endif
                    @if($person->death_cause)
                        @if($some_death_data_printed)
                            &nbsp;&nbsp;
                        @endif
                        {{ $person->death_cause }}<br>
                        @php $some_death_data_printed = true; @endphp
                    @endif
                    @if($person->ageAtDeath())
                        @if($some_death_data_printed)
                            &nbsp;&nbsp;
                        @endif
                        {{ __('people.death_age') }}: {{ trans_choice('misc.year', $person->ageAtDeath()) }}
                    @endif
                </dd>
            @else
                <dd>&#10013;</dd>
            @endif
        @endif

        {{-- funeral --}}
        @if($person->funeral_date || $person->funeral_place)
            @if($person->funeral_date && ! $person->funeral_place)
                <dt>{{ __('people.funeral') }}&nbsp;</dt>
                <dd>{{ $person->funeral_date }}</dd>
            @elseif($person->funeral_place && ! $person->funeral_date)
                <dt>{{ __('people.funeral') }}&nbsp;</dt>
                <dd>{{ $person->funeral_place }}</dd>
            @elseif($person->funeral_place && $person->funeral_date)
                <dt>{{ __('people.funeral') }}&nbsp;</dt>
                <dd>{{ $person->funeral_date }}<br/>&nbsp;&nbsp;{{ $person->funeral_place }}</dd>
            @endif
        @endif

        {{-- burial --}}
        @if($person->burial_date || $person->burial_place)
            @if($person->burial_date && ! $person->burial_place)
                <dt>{{ __('people.burial') }}&nbsp;</dt>
                <dd>{{ $person->burial_date }}</dd>
            @elseif($person->burial_place && ! $person->burial_date)
                <dt>{{ __('people.burial') }}&nbsp;</dt>
                <dd>{{ $person->burial_place }}</dd>
            @elseif($person->burial_place && $person->burial_date)
                <dt>{{ __('people.burial') }}&nbsp;</dt>
                <dd>{{ $person->burial_date }}<br/>&nbsp;&nbsp;{{ $person->burial_place }}</dd>
            @endif
        @endif

        {{-- parents --}}
        @if($person->mother)
            <dt>{{ __('people.mother') }}&nbsp;</dt>
            <dd>@component('components.name', ['person' => $person->mother])@endcomponent</dd>
        @endif
        @if($person->father)
            <dt>{{ __('people.father') }}&nbsp;</dt>
            <dd>@component('components.name', ['person' => $person->father])@endcomponent</dd>
        @endif

        {{-- siblings --}}
        @if($person->siblings->isNotEmpty())
            <dt>{{ __('people.siblings') }} ({{ $person->siblings->count() }})&nbsp;</dt>
            <dd>
                <ul>
                    @foreach($person->siblings as $sibling)
                        <li>@component('components.name', ['person' => $sibling])@endcomponent</li>
                    @endforeach
                </ul>
            </dd>
        @endif

        {{-- przyr. od str. matki --}}
        @if($person->siblings_mother->isNotEmpty())
            <dt>{{ __('people.siblings_mother') }} ({{ $person->siblings_mother->count() }})&nbsp;</dt>
            <dd>
                <ul>
                    @foreach($person->siblings_mother as $sibling)
                        <li>@component('components.name', ['person' => $sibling])@endcomponent</li>
                    @endforeach
                </ul>
            </dd>
        @endif

        {{-- przyr. od str. ojca --}}
        @if($person->siblings_father->isNotEmpty())
            <dt>{{ __('people.siblings_father') }} ({{ $person->siblings_father->count() }})&nbsp;</dt>
            <dd>
                <ul>
                    @foreach($person->siblings_father as $sibling)
                        <li>@component('components.name', ['person' => $sibling])@endcomponent</li>
                    @endforeach
                </ul>
            </dd>
        @endif

        {{-- relationships --}}
        @if($person->marriages->isNotEmpty())
            <dt>{{ __('people.relationships') }}&nbsp;</dt>
            <dd>
                <ul>
                    @foreach($person->marriages as $marriage)
                        <li>
                            @if($marriage->order($person))
                                {{ strtolower(roman($marriage->order($person))) }}.
                            @endif
                            @if($marriage->rite)
                                <strong>{{ __('marriages.rites.' . $marriage->rite) }}:</strong>
                            @endif

                            @component('components.name', ['person' => $marriage->partner($person)])@endcomponent

                            @if(optional(auth()->user())->canWrite())
                                <a
                                    href="{{ route('marriages.edit', ['marriage' => $marriage]) }}"
                                    data-tippy-content="{{ __('marriages.edit_this_marriage') }}">
                                    <small>[{{ __('marriages.marriage') }} №{{ $marriage->id }}]</small>
                                </a>
                                <a href="{{ route('people.create') }}?mother={{ $marriage->woman_id }}&father={{ $marriage->man_id }}"
                                    data-tippy-content="{{ __('marriages.add_child') }}">
                                    <small>[+]</small>
                                </a>
                            @else
                                <small>[marriage №{{ $marriage->id }}]</small>
                            @endif
                            @if($marriage->hasFirstEvent())
                                @if($marriage->first_event_type)
                                    <br>&nbsp;&nbsp;{{ __('marriages.event_types.'.$marriage->first_event_type) }}:
                                @else
                                    <br>&nbsp;
                                @endif
                                @if($marriage->first_event_place && $marriage->first_event_date)
                                    {{ $marriage->first_event_place }}, {{ $marriage->first_event_date }}
                                @elseif($marriage->first_event_place)
                                    {{ $marriage->first_event_place }}
                                @elseif($marriage->first_event_date)
                                    {{ $marriage->first_event_date }}
                                @endif
                            @endif
                            @if($marriage->hasSecondEvent())
                                @if($marriage->second_event_type)
                                    <br>&nbsp;&nbsp;{{ __('marriages.event_types.'.$marriage->second_event_type) }}:
                                @else
                                    <br>&nbsp;
                                @endif
                                @if($marriage->second_event_place && $marriage->second_event_date)
                                    {{ $marriage->second_event_place }}, {{ $marriage->second_event_date }}
                                @elseif($marriage->second_event_place)
                                    {{ $marriage->second_event_place }}
                                @elseif($marriage->second_event_date)
                                    {{ $marriage->second_event_date }}
                                @endif
                            @endif

                            @if($marriage->ended && $marriage->end_date)
                                <br>&nbsp;&nbsp;{{ $marriage->ended != 1 ? $marriage->ended : 'koniec (?)' }}: {{ $marriage->end_date }}
                            @elseif($marriage->ended)
                                <br>&nbsp;&nbsp;{{ $marriage->ended != 1 ? $marriage->ended : 'koniec (?)' }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            </dd>
        @endif

        {{-- children --}}
        @if($person->children->isNotEmpty())
            <dt>{{ __('people.children') }} ({{ $person->children->count() }})&nbsp;</dt>
            <dd>
                <ul>
                    @foreach($person->children as $child)
                        <li>@component('components.name', ['person' => $child])@endcomponent</li>
                    @endforeach
                </ul>
            </dd>
        @endif

        {{-- notes --}}
        {{--<dt>
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
        </dd>--}}
    </dl>

    <br>
    @component('components.letters')
    @endcomponent
@endsection
