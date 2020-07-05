@extends('layouts.app')

@section('content')

    <h1 class="mb-3 mt-4 leading-none text-3xl font-medium">
        <x-person-title-bar :person="$person"/>
    </h1>

    <div class="flex flex-col md:flex-row space-x-2 space-y-2">

        <main class="flex-grow md:w-1/2 p-6 bg-white rounded-lg shadow overflow-hidden">
            <form
                method="POST"
                action="{{ route('people.biography.update', $person) }}">
                @method('patch')
                @csrf

                <div>
                    <fieldset class="w-full flex flex-col">
                        <textarea
                            type="text" class="form-input w-full min-h-full resize-y @error('biography') invalid @enderror"
                            id="biography" name="biography" rows="20"
                            value="{{ old('biography', $person->biography) }}"
                            autofocus>{{ $person->biography }}</textarea>
                        @error('biography')
                            <div class="w-full leading-none mt-1">
                                <small class="text-red-500">{{ $message }}</small>
                            </div>
                        @enderror
                    </fieldset>

                    <fieldset class="-m-6 mt-6 px-6 py-4 bg-gray-50 flex justify-end">
                        <button type="submit" class="btn">
                            {{ __('misc.save') }}
                        </button>
                    </fieldset>
                </div>
            </form>
        </main>

        <div class="flex-shrink-0 p-1">
            <x-person-menu active="biography" :person="$person"/>
        </div>

    </div>

@endsection
