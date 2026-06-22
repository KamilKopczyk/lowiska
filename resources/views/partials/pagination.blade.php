@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Nawigacja stron" class="flex items-center justify-between w-full bg-white/40 backdrop-blur-md p-4 rounded-3xl border border-emerald-500/10 shadow-sm italic">
        {{-- Widok mobilny (prosty) --}}
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-xs font-black text-gray-400 bg-gray-50 border border-gray-200 rounded-xl select-none uppercase tracking-wider">
                    <i class="fa-solid fa-chevron-left mr-1.5"></i> Poprzednia
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-xs font-black text-emerald-700 bg-white border border-emerald-100 rounded-xl hover:bg-emerald-50 transition shadow-sm uppercase tracking-wider">
                    <i class="fa-solid fa-chevron-left mr-1.5"></i> Poprzednia
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-xs font-black text-emerald-700 bg-white border border-emerald-100 rounded-xl hover:bg-emerald-50 transition shadow-sm uppercase tracking-wider">
                    Następna <i class="fa-solid fa-chevron-right ml-1.5"></i>
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 text-xs font-black text-gray-400 bg-gray-50 border border-gray-200 rounded-xl select-none uppercase tracking-wider">
                    Następna <i class="fa-solid fa-chevron-right ml-1.5"></i>
                </span>
            @endif
        </div>

        {{-- Widok desktopowy --}}
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">
                    Wyświetlono <span class="text-emerald-700 font-black">{{ $paginator->firstItem() }}</span> - <span class="text-emerald-700 font-black">{{ $paginator->lastItem() }}</span> z <span class="text-emerald-700 font-black">{{ $paginator->total() }}</span> wyników
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-sm rounded-2xl overflow-hidden border border-emerald-100/50 bg-white p-1 gap-1">
                    {{-- Poprzednia strona --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Poprzednia">
                            <span class="relative inline-flex items-center px-3 py-2 text-sm font-bold text-gray-300 cursor-default select-none" aria-hidden="true">
                                <i class="fa-solid fa-chevron-left"></i>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2 text-sm font-bold text-emerald-600 hover:bg-emerald-50 rounded-xl transition" aria-label="Poprzednia">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif

                    {{-- Elementy paginacji --}}
                    @foreach ($elements as $element)
                        {{-- Wielokropek (...) --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center px-3 py-2 text-sm font-bold text-gray-400 cursor-default select-none">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Tablica linków --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-black text-white bg-emerald-600 rounded-xl shadow-md shadow-emerald-600/20 select-none">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 text-sm font-bold text-gray-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-xl transition" aria-label="Strona {{ $page }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Następna strona --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 text-sm font-bold text-emerald-600 hover:bg-emerald-50 rounded-xl transition" aria-label="Następna">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="Następna">
                            <span class="relative inline-flex items-center px-3 py-2 text-sm font-bold text-gray-300 cursor-default select-none" aria-hidden="true">
                                <i class="fa-solid fa-chevron-right"></i>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
