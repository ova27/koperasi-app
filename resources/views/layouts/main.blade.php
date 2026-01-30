<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Koperasi')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

<div class="flex min-h-screen">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-white border-r">
        @include('layouts.sidebar')
    </aside>

    {{-- WRAPPER KANAN --}}
    <div class="flex-1 flex flex-col">

        {{-- TOP NAVIGATION (LOGOUT ADA DI SINI) --}}
        @include('layouts.navigation')

        {{-- CONTENT --}}
        <main class="flex-1 p-6">
            @yield('content')
        </main>

    </div>

</div>

</body>
</html>
