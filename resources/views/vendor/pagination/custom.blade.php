@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center gap-2">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="text-gray-400 text-sm">← Sebelumnya</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium transition">
                ← Sebelumnya
            </a>
        @endif

        {{-- Page Links --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="text-gray-400 text-sm">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="text-blue-600 font-bold text-sm">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="text-gray-600 hover:text-blue-600 text-sm transition">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium transition">
                Selanjutnya →
            </a>
        @else
            <span class="text-gray-400 text-sm">Selanjutnya →</span>
        @endif
    </nav>
@endif