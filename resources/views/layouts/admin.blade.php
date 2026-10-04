<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Tivity</title>
    @vite(['resources/css/admin.css', 'resources/css/guests.css'])
</head>
<body class="admin-body">
    <header class="admin-header">
        <a href="{{ route('dashboard') }}" class="admin-brand">Tivity <span>Invitation Console</span></a>
        @auth
            <div class="admin-account"><span>{{ auth()->user()->name }}</span><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="link-button">Keluar</button></form></div>
        @endauth
    </header>
    <main class="admin-main">
        @if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
        @yield('content')
    </main>
</body>
</html>
