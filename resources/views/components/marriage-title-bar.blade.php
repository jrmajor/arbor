@if($marriage->woman->dead) <i> @endif
{{ $marriage->woman->name }} {{ $marriage->woman->family_name }}
@if($marriage->woman->dead) </i> @endif

+

@if($marriage->man->dead) <i> @endif
{{ $marriage->man->name }} {{ $marriage->man->family_name }}
@if($marriage->man->dead) </i> @endif

<small class="text-lg">
    [{{ __('marriages.marriage') }} №{{ $marriage->id }}]
</small>
