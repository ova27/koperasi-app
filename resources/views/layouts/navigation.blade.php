<nav x-data="{ open: false }" class="border-b border-slate-200/80 bg-white/80 backdrop-blur-md">
    <!-- Primary Navigation Menu -->
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-20 items-center justify-between sm:h-20">
            <div class="flex min-w-0 items-center gap-2 sm:gap-3">
                {{-- TOGGLE SIDEBAR DESKTOP --}}
                <button id="toggleSidebar"
                    type="button"
                    aria-label="Toggle sidebar"
                    class="z-50 inline-flex h-9 w-9 items-center justify-center rounded-lg border bg-white shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:bg-slate-100 hover:shadow focus:outline-none">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Settings Dropdown -->
            <div class="ms-3 flex min-w-0 flex-shrink-0 items-center">
                <x-dropdown align="right" width="56" contentClasses="py-0 bg-white rounded-xl overflow-hidden">
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
                        <div class="px-4 py-3 border-b border-slate-100">
                            <p class="text-xs font-medium text-slate-500">Masuk sebagai</p>
                            <p class="truncate text-sm font-semibold text-slate-700">{{ Auth::user()->email }}</p>
                        </div>

                        <div class="py-1">
                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="flex w-full items-center gap-2.5 px-4 py-2 text-sm text-red-500 transition-colors duration-150 hover:bg-red-50 hover:text-red-600 focus:outline-none focus:bg-red-50">
                                    <svg class="h-4 w-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                                    </svg>
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

        </div>
    </div>
</nav>
