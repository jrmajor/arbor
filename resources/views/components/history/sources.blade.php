@if(Arr::has($activity['attributes'], $attribute))
  <tr class="block w-full md:table-row md:w-auto">

    <td class="block w-full mt-1 -mb-1 md:m-0 pr-4 md:py-1 md:table-cell md:w-auto">
      <strong>{!! $label ?? __($activity['model']->log_name.'.'.$attribute) !!}</strong>
    </td>

    @if($activity['old'])
      <td class="inline pr-4 md:py-1 md:table-cell">
        @unless(blank($activity['old'][$attribute]))
          @foreach($activity['old'][$attribute] as $source)
            <small>{!! App\Services\Sources\Source::from($source)->markup() !!}</small>@unless($loop->last),@endif
          @endforeach
        @else
          <span class="text-gray-500">{{ __('misc.null') }}</span>
        @endif
      </td>

      <td class="inline pr-4 md:py-1 md:table-cell">=></td>
    @endif

    <td class="inline md:py-1 md:table-cell">
      @unless(blank($activity['attributes'][$attribute]))
        @foreach($activity['attributes'][$attribute] as $source)
          <small>{!! App\Services\Sources\Source::from($source)->markup() !!}</small>@unless($loop->last),@endif
        @endforeach
      @else
        <span class="text-gray-500">{{ __('misc.null') }}</span>
      @endif
    </td>

  </tr>
@endif
