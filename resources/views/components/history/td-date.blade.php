@if(
    Arr::has($activity['attributes'], $attribute.'_from')
    || Arr::has($activity['attributes'], $attribute.'_to')
)
    <tr>
        <td class="pr-5">{!! $label ?? __($activity['model']->log_name.'.'.$attribute) !!}</td>
        @if($activity['old'])
            <td>
                {{
                    format_date_from_period(
                        Carbon\Carbon::create($activity['old'][$attribute.'_from']),
                        Carbon\Carbon::create($activity['old'][$attribute.'_to'])
                    )
                }}
            </td>
            <td>=></td>
        @endif
        @if($activity['attributes'][$attribute.'_from'] !== null)
            <td>
                {{
                    format_date_from_period(
                        Carbon\Carbon::create($activity['attributes'][$attribute.'_from']),
                        Carbon\Carbon::create($activity['attributes'][$attribute.'_to'])
                    )
                }}
            </td>
        @else
            <td class="text-gray-500">null</td>
        @endif
    </tr>
@endif
