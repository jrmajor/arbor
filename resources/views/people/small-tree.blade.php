@if($person->father || $person->mother)
    <div class="p-4 bg-white rounded-lg shadow-lg">
        <div class="text-center">
            <div class="w-full flex text-sm">
                <div class="w-1/2 flex flex-col md:flex-row justify-center">
                    @if($person->father)
                        <div class="w-full md:w-1/2 md:text-right">
                            @if($person->father->father)
                                <x-name :person="$person->father->father" :years="false"/>
                            @endif
                        </div>
                        @if($person->father->father && $person->father->mother)
                            <div class="mx-1">+</div>
                        @endif
                        <div class="w-full md:w-1/2 md:text-left">
                            @if($person->father->mother)
                                <x-name :person="$person->father->mother" :years="false"/>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="w-1/2 flex flex-col md:flex-row justify-center">
                    @if($person->mother)
                        <div class="w-full md:w-1/2 md:text-right">
                            @if($person->mother->father)
                                <x-name :person="$person->mother->father" :years="false"/>
                            @endif
                        </div>
                        @if($person->mother->father && $person->mother->mother)
                            <div class="mx-1">+</div>
                        @endif
                        <div class="w-full md:w-1/2 md:text-left">
                            @if($person->mother->mother)
                                <x-name :person="$person->mother->mother" :years="false"/>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="w-full flex">
                <div class="w-1/2">
                        @if($person->father)
                            @if($person->father->father || $person->father->mother)
                                ↓
                            @endif
                        @endif
                    </div>
                <div class="w-1/2">
                        @if($person->mother)
                            @if($person->mother->father || $person->mother->mother)
                                ↓
                            @endif
                        @endif
                </div>
            </div>

            <div class="w-full flex">
                <div class="w-1/2">
                    @if($person->father)
                        <x-name :person="$person->father"/>
                    @endif
                </div>
                @if($person->father && $person->mother)
                    <div class="mx-1">+</div>
                @endif
                <div class="w-1/2">
                    @if($person->mother)
                        <x-name :person="$person->mother"/>
                    @endif
                </div>
            </div>

            @if($person->father || $person->mother)
                <div class="w-full">
                    ↓
                </div>
            @endif

            <div class="w-full">
                <x-name :person="$person"/>
            </div>
        </div>
    </div>
@endif
