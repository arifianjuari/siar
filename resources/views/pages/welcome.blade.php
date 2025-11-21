<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="0;url={{ auth()->check() ? route('dashboard') : route('login') }}">
    <title>{{ config('app.name', 'SIAR') }}</title>
</head>
<body>
    <p>Mengalihkan ke halaman awal...</p>
</body>
</html> 