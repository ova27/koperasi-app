<nav x-data="{ open: false }" class="border-b border-slate-200/80 bg-white/80 backdrop-blur-md">
    <!-- Primary Navigation Menu -->
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-14 items-center justify-between sm:h-16">
            <div class="flex min-w-0 items-center gap-2 sm:gap-3">
                {{-- TOGGLE SIDEBAR DESKTOP --}}
                <button id="toggleSidebar"
                    type="button"
                    aria-label="Toggle sidebar"
                    class="z-50 inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:bg-slate-100 hover:shadow focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <div class="min-w-0">
                    <div class="truncate text-sm font-semibold text-slate-700">Koperasi Simpatik</div>
                    <div class="truncate text-xs text-slate-500">BPS Provinsi Banten</div>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="ms-3 flex min-w-0 flex-shrink-0 items-center">
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button
                            type="button"
                            class="inline-flex max-w-[72vw] items-center gap-2 rounded-xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white px-2 py-1.5 text-left text-sm text-slate-700 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow focus:outline-none focus:ring-2 focus:ring-sky-500 sm:max-w-none sm:gap-3 sm:px-3 sm:py-2"
                        >
                            <span class="inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-sky-500 to-blue-600 text-xs font-semibold text-white shadow-sm">
                                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                            </span>

                            <span class="min-w-0">
                                <span class="block truncate text-sm font-semibold text-slate-700">
                                    {{ Auth::user()->name }}
                                </span>
                                <span class="block truncate text-[11px] leading-tight text-slate-500 capitalize">
                                    {{ auth()->user()->getRoleNames()->implode(', ') }}
                                </span>
                            </span>

                            <span class="ms-0.5 flex-shrink-0 text-slate-500">
                                <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Ubah Profil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

        </div>
    </div>
</nav>
