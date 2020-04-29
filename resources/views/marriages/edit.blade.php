@extends('layouts.app')

@section('content')
    <h3>
        {{ __('marriages.edit_marriage') }}

        <small class="text-lg">[№{{ $marriage->id }}]</small>
    </h3>

    @component('marriages.form', ['marriage' => $marriage, 'action' => 'edit']) @endcomponent
@endsection
