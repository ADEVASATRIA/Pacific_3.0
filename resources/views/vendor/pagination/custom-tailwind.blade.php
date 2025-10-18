@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center mt-6 mb-6">
        <ul class="inline-flex items-center space-x-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span class="px-3 py-1 rounded-lg bg-gray-200 text-gray-500 cursor-not-allowed">Prev</span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Prev</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li><span class="px-3 py-1 text-gray-500">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li><span class="px-3 py-1 rounded-lg bg-blue-600 text-white">{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}" class="px-3 py-1 rounded-lg bg-gray-100 hover:bg-blue-100 text-gray-700">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Next</a>
                </li>
            @else
                <li>
                    <span class="px-3 py-1 rounded-lg bg-gray-200 text-gray-500 cursor-not-allowed">Next</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
