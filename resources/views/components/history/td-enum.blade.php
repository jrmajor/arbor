@if(Arr::has($activity['attributes'], $attribute))
    <tr>
        <td class="pr-5">{!! $label ?? __($activity['model']->log_name.'.'.$attribute) !!}</td>
        @if($activity['old'])
            @if($activity['old'][$attribute] !== null)
                <td>{{ __($translations.'.'.$activity['old'][$attribute]) }}</td>
            @else
                <td class="text-gray-500">{{ __('misc.null') }}</td>
            @endif
            <td>=></td>
        @endif
        @if($activity['attributes'][$attribute] !== null)
            <td>{{ __($translations.'.'.$activity['attributes'][$attribute]) }}</td>
        @else
            <td class="text-gray-500">{{ __('misc.null') }}</td>
        @endif
    </tr>
@endif
