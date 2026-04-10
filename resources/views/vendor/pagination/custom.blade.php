@if ($paginator->hasPages())
    <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden text-sm">

        {{-- PREVIOUS --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1 text-gray-400 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" 
                    class="w-4 h-4"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
            class="group px-3 py-1 flex items-center text-gray-600 hover:bg-gray-50 transition-all duration-200">

                <svg xmlns="http://www.w3.org/2000/svg" 
                    class="w-4 h-4 transition-transform duration-200 group-hover:-translate-x-0.5"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
        @endif


        {{-- PAGE NUMBERS --}}
        @foreach ($elements as $element)

            {{-- DOTS --}}
            @if (is_string($element))
                <span class="px-3 py-1 text-gray-400 border-l border-gray-200">
                    {{ $element }}
                </span>
            @endif

            {{-- NUMBERS --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)

                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-1 font-semibold text-gray-800 bg-gray-100 border-l border-gray-200">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                        class="px-3 py-1 text-gray-600 hover:bg-gray-50 hover:text-gray-800 border-l border-gray-200 transition-all duration-200">
                            {{ $page }}
                        </a>
                    @endif

                @endforeach
            @endif

        @endforeach


        {{-- NEXT --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
            class="group px-3 py-1 flex items-center text-gray-600 hover:bg-gray-50 border-l border-gray-200 transition-all duration-200">

                <svg xmlns="http://www.w3.org/2000/svg" 
                    class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-0.5"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        @else
            <span class="px-3 py-1 text-gray-400 flex items-center border-l border-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" 
                    class="w-4 h-4"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </span>
        @endif

    </div>
@endif