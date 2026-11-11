
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SafeRoad')</title>

    <link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">
    @stack('styles')
</head>
<body>

    <main class="app-container">
        @yield('content')
    </main>

    @include('components.bottom-navigation')

    <script src="{{ asset('assets/js/profile.js') }}"></script>
    @stack('scripts')
</body>
</html>
