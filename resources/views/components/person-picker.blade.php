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

<div class="flex flex-col"
    x-data="personPickerData{{ $name }}()"
    x-init="
        selected.id = initial.id;
        selected.name = initial.name;
    ">
    <label for="{{ $name }}_search" class="w-full font-medium pb-1 text-gray-700">{{ $label }}</label>
    <div class="w-full">
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
            <div class="relative block cursor-text form-select
                    @error($name.'_id') shadow-outline-red @else active:shadow-outline-blue focus-within:shadow-outline-blue @enderror"
                @click="$refs.search.focus()">
                <div class="pr-4">
                    <span x-text="selected.name">
                    </span>{{--
                    --}}<input
                        type="text" class="appearance-none outline-none text-gray-600 focus:text-gray-800"
                        :style="selected.id != null ? 'width: 4px' : 'width: 100%'" autocomplete="off"
                        x-ref="search" x-model="search" id="{{ $name }}_search"
                        @focus="open = true"
                        @keydown="keyEvent($event)" @keypress="keyEvent($event)" @keyup="keyEvent($event)" @paste="keyEvent($event)">
                </div>
            </div>
            <template x-if="open">
                <div
                    class="absolute mt-1.5 z-50 py-1 w-full text-gray-800 bg-white rounded-md shadow-md border border-gray-300">
                    <template x-if="people.length == 0">
                        <div class="w-full px-3 py-1 text-gray-600">
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
                            class="flex w-full px-3 py-1 text-gray-800 text-left justify-between hover:bg-cool-gray-100">
                            <span x-text="person.name"></span>
                            <span class="text-gray-400" x-text="selected.id == person.id ? '✓ ' : ''"></span>
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
