@props(['date', 'place', 'prefix' => '', 'multiline' => false])

@php

use Illuminate\Support\HtmlString;

$slotWithColon = new HtmlString($slot->isNotEmpty() ? $slot . ': ' : '');

@endphp

@if ($place !== null && $date !== null)
  @if ($multiline)
    <p>{{ $date }}</p>
    <p>{{ $place }}</p>
  @else
    <p>{{ $slotWithColon }}{{ $place }}, {{ $date }}</p>
  @endif
@elseif ($place !== null)
  <p>{{ $slotWithColon }}{{ $place }}</p>
@elseif ($date !== null)
  <p>{{ $slotWithColon }}{{ $date }}</p>
@elseif ($slot->isNotEmpty())
  <p>{{ $slot }}</p>
@endif
