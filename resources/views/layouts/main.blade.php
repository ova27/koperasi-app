<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Koperasi')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800">

<div class="flex min-h-screen">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-white border-r shadow-sm">
        @include('layouts.sidebar')
    </aside>

    {{-- WRAPPER KANAN --}}
    <div class="flex-1 flex flex-col">

        {{-- TOP NAVIGATION --}}
        <header class="bg-white border-b shadow-sm">
            @include('layouts.navigation')
        </header>

        {{-- CONTENT --}}
        <main class="flex-1 p-6">
            <div class="max-w-7xl mx-auto">

                {{-- PAGE TITLE --}}
                @hasSection('page-title')
                    <h1 class="text-xl font-semibold mb-4">
                        @yield('page-title')
                    </h1>
                @endif

                {{-- CARD CONTENT --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    @yield('content')
                </div>

            </div>
        </main>

    </div>

</div>

</body>
</html>
