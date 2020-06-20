@push('scripts')
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
@endpush

<script>
    function personPickerData{{ $name }}() {
        return {
            open: false,
            nullable: {{ $nullable ? 'true' : 'false' }},
            initial: {
                id: {{ optional($initial)->id ?: 'null' }},
                name: {!! "'".e(optional($initial)->formatName())."'" ?: 'null' !!}
            },
            selected: {
                id: null,
                name: null,
            },
            previousSearch: '',
            search: '',
            people: [],
            keyEvent(e) {
                if (e.metaKey || e.altKey) return;

                if (this.selected.id == null) {
                    if(this.search != this.previousSearch) {
                        axios.get('{{ route('people.picker') }}', {
                            params: {
                                @if($sex ?? null)
                                    sex: '{{ $sex }}',
                                @endif
                                search: this.search
                            }
                        })
                        .then(response => {
                            this.people = response.data;
                        })
                        .catch(response => {
                            console.log(response);
                        });
                        this.previousSearch = this.search;
                    }
                } else if (e.keyCode == 8) {
                    this.selected.id = null;
                    this.selected.name = null;
                    this.search = '';
                } else {
                    e.preventDefault();
                }
            }
        }
    }
</script>

<div class="flex flex-wrap"
    x-data="personPickerData{{ $name }}()"
    x-init="
        selected.id = initial.id;
        selected.name = initial.name;
    ">
    <label for="{{ $name }}_search" class="w-full sm:w-1/2 md:w-1/4 pr-1 py-1">{{ $label }}</label>
    <div class="w-full sm:w-1/2 md:w-3/4 lg:w-1/2">
        <input
            type="hidden"
            name="{{ $name }}_id"
            x-model="selected.id">
        <div class="relative w-full"
            @mousedown.away="
                open = false;
                if({{ $nullable ? 'false' : 'true' }} && initial.id != null) {
                    selected.id = initial.id;
                    selected.name = initial.name;
                    search = null;
                }
            ">
            <div class="relative block px-3 py-1 w-full rounded border border-gray-500 text-gray-800 bg-white cursor-text
                    @error($name.'_id') border-red-500 @else active:border-blue-600 focus-within:border-blue-600 @enderror"
                :class="{ '!rounded-b-none': open }"
                :style="open ? 'border-bottom-width: 0; margin-bottom: 1px' : ''"
                @click="$refs.search.focus()">
                <div class="pr-4">
                    <span x-text="selected.name">
                    </span>{{--
                    --}}<input
                        type="text" class="appearance-none outline-none text-gray-600 focus:text-gray-800"
                        :style="selected.id != null ? 'width: 4px' : 'width: 100%'"
                        x-ref="search" x-model="search" id="{{ $name }}_search"
                        @focus="open = true"
                        @keydown="keyEvent($event)" @keypress="keyEvent($event)" @keyup="keyEvent($event)" @paste="keyEvent($event)">
                </div>
                <div class="absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg class="fill-current h-4 w-4" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                    </svg>
                </div>
            </div>
            <template x-if="open">
                <div
                    class="absolute z-10 w-full text-gray-800 bg-white rounded-b border-l border-r border-b
                    @error($name.'_id') border-red-500 @else border-blue-600 @enderror"
                    style="margin-top: -1px">
                    <template x-if="people.length == 0">
                        <div class="w-full px-3 py-1 text-gray-600 border-t border-gray-300">
                            {{ __('misc.no_results') }}
                        </div>
                    </template>
                    <template x-for="person in people" x-key="person.id">
                        <button
                            @click.prevent="
                                selected.id = person.id;
                                selected.name = person.name;
                                search = null;
                                open = false;
                            "
                            class="flex w-full px-3 py-1 border-t border-gray-300 text-gray-800 justify-between">
                            <span x-text="person.name"></span>
                            <span x-text="selected.id == person.id ? '✓ ' : ''"></span>
                        </button>
                    </template>
                </div>
            </template>
        </div>
        @error($name.'_id')
            <small class="text-red-500">{{ $message }}</small>
        @enderror
    </div>
</div>
