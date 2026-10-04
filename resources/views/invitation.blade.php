<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#230e15">
    <meta name="description" content="Dengan penuh cinta, kami mengundang Anda merayakan pernikahan {{ $invitation->title }}.">
    <title>{{ $invitation->title }} — A Love Written in the Stars</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preload" href="{{ asset('fonts/cormorant-regular.ttf') }}" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="{{ asset('fonts/dm-sans-regular.ttf') }}" as="font" type="font/ttf" crossorigin>
    <script>window.__WEDDING__ = @json($frontendData);</script>
    @vite('resources/js/invitation.js')
</head>
<body><div id="app"></div></body>
</html>
