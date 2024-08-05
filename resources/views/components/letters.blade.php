<div>
  <h2>{{ __('people.index.by_family_name') }}:</h2>

  <ul class="columns-3 xs:columns-4 sm:columns-5 md:columns-6 lg:columns-8">
    @foreach ($letters('family') as $letter)
      <li>
        @if ($isActive($letter, 'f'))
          <strong>
        @endif
        <a href="{{ route('people.letter', ['type' => 'f', 'letter' => urlencode($letter->letter)]) }}" class="a">
          {{ $letter->letter }} <small>[{{ $letter->count }}]</small>
        </a>
        @if ($isActive($letter, 'f'))
          </strong>
        @endif
      </li>
    @endforeach
  </ul>
</div>

<div class="mt-4">
  <h2>{{ __('people.index.by_last_name') }}:</h2>

  <ul class="columns-3 xs:columns-4 sm:columns-5 md:columns-6 lg:columns-8">
    @foreach ($letters('last') as $letter)
      <li>
        @if ($isActive($letter, 'l'))
          <strong>
        @endif
        <a href="{{ route('people.letter', ['type' => 'l', 'letter' => urlencode($letter->letter)]) }}" class="a">
          {{ $letter->letter }} <small>[{{ $letter->count }}]</small>
        </a>
        @if ($isActive($letter, 'l'))
          </strong>
        @endif
      </li>
    @endforeach
  </ul>
</div>
