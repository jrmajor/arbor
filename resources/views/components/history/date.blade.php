@if(
    Arr::has($activity['attributes'], $attribute.'_from')
    || Arr::has($activity['attributes'], $attribute.'_to')
)
    <tr class="block w-full md:table-row md:w-auto">

        <td class="block w-full mt-1 -mb-1 md:m-0 pr-4 md:py-1 md:table-cell md:w-auto">
            <strong>{!! $label ?? __($activity['model']->log_name.'.'.$attribute) !!}</strong>
        </td>

        @if($activity['old'])
            <td class="inline pr-4 md:py-1 md:table-cell">
                @unless($activity['old'][$attribute.'_from'] === null && $activity['old'][$attribute.'_to'] === null)
                    {{
                        Carbon\Carbon::create($activity['old'][$attribute.'_from'])
                            ->formatPeriodTo(Carbon\Carbon::create($activity['old'][$attribute.'_to']))
                    }}
                @else
                    <span class="text-gray-500">null</span>
                @endif
            </td>

            <td class="inline pr-4 md:py-1 md:table-cell">=></td>
        @endif

        <td class="inline md:py-1 md:table-cell">
            @unless($activity['attributes'][$attribute.'_from'] === null && $activity['attributes'][$attribute.'_to'] === null)
                {{
                    Carbon\Carbon::create($activity['attributes'][$attribute.'_from'])
                        ->formatPeriodTo(Carbon\Carbon::create($activity['attributes'][$attribute.'_to']))
                }}
            @else
                <span class="text-gray-500">null</span>
            @endif
        </td>

    </tr>
@endif
