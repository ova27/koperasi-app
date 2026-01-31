@php
    $menus = config('sidebar');
    $user  = auth()->user();

    // semua user adalah anggota
    $userRoles = collect([
        'anggota',
        $user->jabatan, // ketua | bendahara | null
    ])->filter()->toArray();
@endphp

<nav class="p-4 space-y-1">

    @foreach ($menus as $menu)

        @if (count(array_intersect($userRoles, $menu['roles'])) > 0)

            @php
                $activeRoutes = (array) ($menu['active_routes'] ?? $menu['route']);
                $isActive = false;

                foreach ($activeRoutes as $route) {
                    if (request()->routeIs($route)) {
                        $isActive = true;
                        break;
                    }
                }
            @endphp

            <a href="{{ route($menu['route']) }}"
               class="sidebar-link {{ $isActive ? 'active' : '' }}">

                <x-sidebar-icon :name="$menu['icon']" />
                <span>{{ $menu['label'] }}</span>

            </a>

        @endif

    @endforeach

</nav>
