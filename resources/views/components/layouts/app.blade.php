<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Online&Go' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-200 dark:bg-slate-700">

    {{-- Navbar --}}
    @livewire('partials.navbar')

    {{-- Main Content --}}
    <main>
        {{ $slot }}
    </main>

    {{-- Footer --}}
    @livewire('partials.footer')

    {{-- Livewire Scripts --}}
    @livewireScripts

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- LivewireAlert v4 Scripts --}}
    @livewireAlertScripts

</body>
</html>
