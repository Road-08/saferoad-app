@php($class = $class ?? 'icon')

<svg
    viewBox="0 0 24 24"
    class="{{ $class }}"
    fill="none"
    stroke="currentColor"
    stroke-width="1.8"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
>
    @switch($name)
        @case('shield')
            <path d="M12 3 5 6v5c0 4.7 2.9 8.6 7 10 4.1-1.4 7-5.3 7-10V6l-7-3Zm-3 9 2 2 4-4" />
            @break

        @case('mail')
            <rect x="3" y="5" width="18" height="14" rx="2" />
            <path d="m3 7 9 6 9-6" />
            @break

        @case('lock')
            <rect x="4" y="10" width="16" height="11" rx="2" />
            <path d="M8 10V7a4 4 0 0 1 8 0v3" />
            @break

        @case('user')
            <circle cx="12" cy="8" r="4" />
            <path d="M4 21a8 8 0 0 1 16 0" />
            @break

        @case('eye')
            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" />
            <circle cx="12" cy="12" r="2.5" />
            @break

        @case('upload')
            <path d="M12 16V4m0 0L7 9m5-5 5 5" />
            <path d="M5 14v5h14v-5" />
            @break

        @case('arrow')
            <path d="M5 12h14m-5-5 5 5-5 5" />
            @break

        @case('check')
            <path d="m5 12 4 4L19 6" />
            @break

        @case('spark')
            <path d="M12 2v4m0 12v4M4.9 4.9l2.8 2.8m8.6 8.6 2.8 2.8M2 12h4m12 0h4M4.9 19.1l2.8-2.8m8.6-8.6 2.8-2.8" />
            @break
    @endswitch
</svg>
