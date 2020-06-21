<div class="p-4 bg-white rounded-lg shadow-lg">
    <dl>

        {{-- pytlewski --}}
        @if($pytlewski = $person->pytlewski)
            <dt>@lang('people.pytlewski.id')&nbsp;</dt>
            <dd x-data="{ open: false }">
                <a href="{{ $pytlewski->url }}" target="_blank" class="a">
                    {{ $pytlewski->id}}
                    @if($pytlewski->name || $pytlewski->family_name || $pytlewski->last_name)
                        <small class="text-gray-700">
                            {{ __('people.pytlewski.as') }}
                            <strong>{{ $pytlewski->last_name
                                                ? $pytlewski->last_name.' ('.$pytlewski->family_name.')'
                                                : $pytlewski->family_name }}</strong>
                            {{ $pytlewski->name }} {{ $pytlewski->middle_name }}
                        </small>
                    @endif
                </a>
                <button @click="open = true"
                    class="btn leading-none text-xs !px-2">
                    {{ __('people.pytlewski.show_more') }}
                </button>
                <br>
                <div x-show="open" @click.away="open = false" style="display: none;">
                    <small style="display: block; line-height: 1.45">
                        @if($pytlewski->hasParents())
                            &nbsp;&nbsp;{{ __('people.pytlewski.parents') }}: <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <x-pytlewski :id="$pytlewski->mother_id">
                                {{ $pytlewski->mother_surname }}, {{ $pytlewski->mother_name }}
                            </x-pytlewski>
                            <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <x-pytlewski :id="$pytlewski->father_id">
                                {{ $pytlewski->father_surname }}, {{ $pytlewski->father_name }}
                            </x-pytlewski>
                            <br>
                        @endif

                        @if($pytlewski->hasMarriages())
                            &nbsp;&nbsp;{{ __('people.pytlewski.marriages') }}:<br>
                            @foreach($pytlewski->marriages as $marriage)
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <x-pytlewski :id="$marriage['id'] ?? null">
                                    {{ $marriage['name'] ?? null }}
                                </x-pytlewski>
                                <br>
                            @endforeach
                        @endif

                        @if($pytlewski->hasChildren())
                            &nbsp;&nbsp;{{ __('people.pytlewski.children') }}:<br>
                            @foreach($pytlewski->children as $child)
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <x-pytlewski :id="$child['id'] ?? null">
                                    {{ $child['name'] ?? null }}
                                </x-pytlewski>
                                <br>
                            @endforeach
                        @endif

                        @if($pytlewski->hasSiblings())
                            &nbsp;&nbsp;{{ __('people.pytlewski.siblings') }}:<br>
                            @foreach($pytlewski->siblings as $sibling)
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <x-pytlewski :id="$sibling['id'] ?? null">
                                    {{ $sibling['name'] ?? null }}
                                </x-pytlewski>
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
                <a href="{{ $wielcy->url }}" target="_blank" class="a">
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
                    @if(! $person->dead && $person->currentAge() !== null)
                        @if($some_birth_data_printed)
                            &nbsp;&nbsp;
                        @endif
                        {{ __('people.current_age') }}:
                        {{
                            trans_choice(
                                'misc.year',
                                $person->currentAge(true),
                                ['age' => $person->currentAge()]
                            )
                        }}
                        <br>
                        @php $some_birth_data_printed = true; @endphp
                    @endif
                    @if(
                        (! $person->birth_date || optional(auth()->user())->isSuperAdmin())
                        && $person->estimatedBirthDate()
                    )
                        @if($some_birth_data_printed == true)
                            &nbsp;&nbsp;
                        @endif
                        {{ __('people.estimated_birth_date') }}: {!! $person->estimatedBirthDate() !!}
                        @if($person->estimatedBirthDateError())
                            <small>
                                (<strong>{{ $person->estimatedBirthDateError() }}</strong>
                                {{ trans_choice('misc.years_of_error', $person->estimatedBirthDateError()) }})
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
                    @if($person->ageAtDeath() !== null)
                        @if($some_death_data_printed)
                            &nbsp;&nbsp;
                        @endif
                        {{ __('people.death_age') }}:
                        {{
                            trans_choice(
                                'misc.year',
                                $person->ageAtDeath(true),
                                ['age' => $person->ageAtDeath()]
                            )
                        }}
                    @endif
                </dd>
            @else
                <dd>&#10013;&#xFE0E;</dd>
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
            <dd><x-name :person="$person->mother"/></dd>
        @endif
        @if($person->father)
            <dt>{{ __('people.father') }}&nbsp;</dt>
            <dd><x-name :person="$person->father"/></dd>
        @endif

        {{-- siblings --}}
        @if($person->siblings->isNotEmpty())
            <dt>{{ __('people.siblings') }} ({{ $person->siblings->count() }})&nbsp;</dt>
            <dd>
                <ul>
                    @foreach($person->siblings as $sibling)
                        <li><x-name :person="$sibling"/></li>
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
                        <li><x-name :person="$sibling"/></li>
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
                        <li><x-name :person="$sibling"/></li>
                    @endforeach
                </ul>
            </dd>
        @endif

        {{-- marriages --}}
        @if($person->marriages->isNotEmpty())
            <dt>{{ __('people.marriages') }}&nbsp;</dt>
            <dd>
                <ul>
                    @foreach($person->marriages as $marriage)
                        @if($marriage->canBeViewedBy(auth()->user()))
                            <li>
                                @if($marriage->order($person))
                                    {{ strtolower(roman($marriage->order($person))) }}.
                                @endif
                                @if($marriage->rite)
                                    <strong>{{ __('marriages.rites.' . $marriage->rite) }}:</strong>
                                @endif

                                <x-name :person="$marriage->partner($person)"/>

                                @if(optional(auth()->user())->canWrite())
                                    <a
                                        href="{{ route('marriages.edit', ['marriage' => $marriage]) }}"
                                        data-tippy-content="{{ __('marriages.edit_this_marriage') }}"
                                        class="a">
                                        <small>[{{ __('marriages.marriage') }} №{{ $marriage->id }}]</small>
                                    </a>
                                    <a href="{{ route('people.create', [
                                                            'mother' => $marriage->woman_id,
                                                            'father' => $marriage->man_id,
                                        ]) }}"
                                        data-tippy-content="{{ __('marriages.add_child') }}"
                                        class="a">
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

                                @if($marriage->divorced)
                                    @if($marriage->divorce_place && $marriage->divorce_date)
                                        <br>&nbsp;&nbsp;{{ strtolower(__('marriages.divorced')) }}: {{ $marriage->divorce_place }}, {{ $marriage->divorce_date }}
                                    @elseif($marriage->divorce_place)
                                        <br>&nbsp;&nbsp;{{ strtolower(__('marriages.divorced')) }}: {{ $marriage->divorce_place }}
                                    @elseif($marriage->divorce_date)
                                        <br>&nbsp;&nbsp;{{ strtolower(__('marriages.divorced')) }}: {{ $marriage->divorce_date }}
                                    @else
                                        <br>&nbsp;&nbsp;{{ strtolower(__('marriages.divorced')) }}
                                    @endif
                                @endif
                            </li>
                        @else
                            <li>
                                @if($marriage->order($person))
                                    {{ strtolower(roman($marriage->order($person))) }}.
                                @endif

                                <small>[{{ __('misc.hidden') }}]</small>
                                <small>[marriage №{{ $marriage->id }}]</small>
                            </li>
                        @endif
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
                        <li><x-name :person="$child"/></li>
                    @endforeach
                </ul>
            </dd>
        @endif

        {{-- sources --}}
        @if($person->sources->isNotEmpty())
            <dt>{{ __('people.sources') }}&nbsp;</dt>
            <dd>
                <ul>
                    @foreach($person->sources as $source)
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
