@extends('layouts.app')

@section('content')
    <h3>{{ __('marriages.add_a_new_marriage') }}</h3>

    @component('marriages.form', ['marriage' => $marriage, 'action' => 'create']) @endcomponent
@endsection
