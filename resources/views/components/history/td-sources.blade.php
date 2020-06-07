@if(Arr::has($activity['attributes'], $attribute))
    <tr>
        <td class="pr-5">{!! $label ?? __($activity['model']->log_name.'.'.$attribute) !!}</td>
        @if($activity['old'])
            @if(! blank($activity['old'][$attribute]))
                <td>
                    @foreach($activity['old'][$attribute] as $source)
                        <small>{!! App\Source::from($source)->markup() !!}</small>@if(! $loop->last),@endif
                    @endforeach
                </td>
            @else
                <td class="text-gray-500">{{ __('misc.null') }}</td>
            @endif
            <td>=></td>
        @endif
        @if(! blank($activity['attributes'][$attribute]))
            <td>
                @foreach($activity['attributes'][$attribute] as $source)
                    <small>{!! App\Source::from($source)->markup() !!}</small>@if(! $loop->last),@endif
                @endforeach
            </td>
        @else
            <td class="text-gray-500">{{ __('misc.null') }}</td>
        @endif
    </tr>
@endif
