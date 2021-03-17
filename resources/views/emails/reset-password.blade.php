{{ __('passwords.you_are_receiving') }}

{{ __('passwords.you_can_use_this_link') }}: {{ $url }}

{{ __('passwords.reset_link_will_expire', [
    'count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
]) }}

{{ __('passwords.if_you_didnt_request') }}
