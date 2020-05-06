@if(Arr::has($activity['attributes'], 'sex'))
    <tr>
        <td class="pr-5">{!! $label ?? __($activity['model']->log_name.'.sex') !!}</td>
        @if($activity['old'])
            @if($activity['old']['sex'] !== null)
                <td>{{ $activity['old']['sex'] == 'xx' ? __('people.female') : __('people.male') }}</td>
            @else
                <td class="text-gray-500">{{ __('misc.null') }}</td>
            @endif
            <td>=></td>
        @endif
        @if($activity['attributes']['sex'] !== null)
            <td>{{ $activity['attributes']['sex'] == 'xx' ? __('people.female') : __('people.male') }}</td>
        @else
            <td class="text-gray-500">{{ __('misc.null') }}</td>
        @endif
    </tr>
@endif
