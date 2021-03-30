<div>
  <h2>{{ __('people.index.by_family_name') }}:</h2>

  <ul class="col-count-3 xs:col-count-4 sm:col-count-5 md:col-count-6 lg:col-count-8">
    @foreach ($letters('family') as $letter)
      <li>
        @if ($isActive($letter, 'f'))
          <strong>
        @endif
        <a href="{{ route('people.letter', ['type' => 'f', 'letter' => $letter->letter]) }}" class="a">
          {{ $letter->letter }} <small>[{{ $letter->total }}]</small>
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

  <ul class="col-count-3 xs:col-count-4 sm:col-count-5 md:col-count-6 lg:col-count-8">
    @foreach ($letters('last') as $letter)
      <li>
        @if ($isActive($letter, 'l'))
          <strong>
        @endif
        <a href="{{ route('people.letter', ['type' => 'l', 'letter' => $letter->letter]) }}" class="a">
          {{ $letter->letter }} <small>[{{ $letter->total }}]</small>
        </a>
        @if ($isActive($letter, 'l'))
          </strong>
        @endif
      </li>
    @endforeach
  </ul>
</div>
