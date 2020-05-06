@if(Arr::has($activity['attributes'], $attribute))
    <tr>
        <td class="pr-5">{!! $label ?? __($activity['model']->log_name.'.'.$attribute) !!}</td>
        @if($activity['old'])
            @if($activity['old'][$attribute] !== null)
                @if(is_bool($activity['old'][$attribute]))
                    <td>{{ $activity['old'][$attribute] ? __('misc.yes') : __('misc.no') }}</td>
                @else
                    <td>{{ $activity['old'][$attribute] }}</td>
                @endif
            @else
                <td class="text-gray-500">{{ __('misc.null') }}</td>
            @endif
            <td>=></td>
        @endif
        @if($activity['attributes'][$attribute] !== null)
            @if(is_bool($activity['attributes'][$attribute]))
                <td>{{ $activity['attributes'][$attribute] ? __('misc.yes') : __('misc.no') }}</td>
            @else
                <td>{{ $activity['attributes'][$attribute] }}</td>
            @endif
        @else
            <td class="text-gray-500">{{ __('misc.null') }}</td>
        @endif
    </tr>
@endif
