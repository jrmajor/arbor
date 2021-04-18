@if ($active)

  <span class="text-blue-700 transition">
    <li class="px-3 py-1 rounded transition">
      <span class="w-full border-b-2 border-dotted border-blue-500 flex items-center">
        <svg class="h-4 w-4 mr-2 fill-current text-blue-600 transition"
          viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"
        >
          {{ $slot }}
        </svg>
        {{ __($name) }}
      </span>
    </li>
  </span>

@else

  <a href="{{ $route }}"
    @if ($form)
      onclick="
        event.preventDefault();

        document
          .getElementById('{{ $form['name'] }}-form')
          .submit();
      "
    @endif
    class="group focus:outline-none transition
      {{ $danger ? 'text-red-600 hover:text-red-700 focus:text-red-700' : 'text-gray-700 hover:text-gray-800 focus:text-gray-800' }}"
  >
    <li class="px-3 py-1 rounded transition
      {{ $danger ? 'group-hover:bg-red-200 group-focus:bg-red-300' : 'group-hover:bg-gray-200 group-focus:bg-gray-200' }}"
    >
      <span class="w-full flex items-center">
        <svg class="h-4 w-4 mr-2 fill-current transition
          @if (! $danger) text-gray-600 group-hover:text-gray-700 group-focus:text-gray-700 @endif"
          viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"
        >
          {{ $slot }}
        </svg>
        {{ __($name) }}
      </span>
    </li>
    @if ($form)
      <form id="{{ $form['name'] }}-form" method="POST" action="{{ $route }}" style="display: none">
        @method($form['method'])
        @csrf
        {{ $formBody ?? '' }}
      </form>
    @endif
  </a>

@endif
