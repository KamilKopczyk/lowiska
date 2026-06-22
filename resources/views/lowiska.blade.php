<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Wszystkie Łowiska - Wędkarski Radar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2">
    <link rel="apple-touch-icon" href="/favicon.svg?v=2">
    <style>
        /* Zapobiega zaznaczaniu tekstu podczas jeżdżenia myszką po gwiazdkach */
        .no-select { user-select: none; -webkit-user-select: none; }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-800 flex flex-col min-h-screen">

    <nav id="main-navbar" class="sticky top-0 z-50 bg-emerald-900/95 backdrop-blur-md text-white shadow-md transition-all duration-300">
        <div id="main-navbar-container" class="container mx-auto flex flex-wrap justify-between items-center px-4 py-4 transition-all duration-300">
            <div class="flex items-center justify-between w-full md:w-auto">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-fish-fins text-2xl text-amber-400"></i>
                    <a href="/" class="text-xl font-bold tracking-tight hover:text-gray-200">Wędkarski Radar</a>
                </div>
                <button type="button" onclick="toggleMobileMenu()" class="md:hidden text-emerald-200 hover:text-white focus:outline-none">
                    <i class="fa-solid fa-bars text-2xl"></i>
                </button>
            </div>
            
            <div id="mobile-menu" class="hidden md:flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-6 font-medium text-sm w-full md:w-auto mt-4 md:mt-0 pb-4 md:pb-0 transition-all duration-300">
                <a href="/lowiska" class="text-amber-400 flex items-center border-b-2 border-amber-400 pb-1 w-fit">
                    <i class="fa-solid fa-water mr-2"></i> Wszystkie łowiska
                </a>
                <a href="/mapa" class="hover:text-amber-400 transition flex items-center w-fit">
                    <i class="fa-solid fa-map mr-2"></i> Mapa
                </a>
                @guest
                    <button type="button" onclick="openLoginModal()" class="bg-amber-500 hover:bg-amber-600 text-emerald-950 font-bold px-5 py-2 rounded-lg transition flex items-center shadow-md border border-amber-400/50 w-full md:w-auto justify-center">
                        <i class="fa-solid fa-arrow-right-to-bracket mr-2 text-emerald-950"></i> Zaloguj się
                    </button>
                @endguest

                @auth
                    <div class="flex items-center justify-between w-full md:w-auto space-x-4">
                        <a href="/profil" class="bg-emerald-700 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg transition flex items-center shadow-sm border border-emerald-600/50 w-full justify-center">
                            @if(Auth::user()->isAdmin())
                                <i class="fa-solid fa-user-shield mr-2 text-amber-400 text-lg"></i>
                            @else
                                <i class="fa-solid fa-user-astronaut mr-2 text-amber-400 text-lg"></i>
                            @endif
                            <span class="font-bold">{{ Auth::user()->name }}</span>
                        </a>

                        <form method="POST" action="/logout" class="m-0 p-0 flex shrink-0">
                            @csrf
                            <button type="submit" class="text-emerald-200 hover:text-red-400 transition text-sm font-medium flex items-center bg-emerald-800 md:bg-transparent px-3 py-2 md:p-0 rounded-lg md:rounded-none" title="Wyloguj się">
                                <i class="fa-solid fa-power-off text-lg"></i>
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8 flex-grow max-w-7xl flex flex-col md:flex-row gap-8">

        <aside class="w-full md:w-1/4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sticky top-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Wyszukiwarka</h2>

                <form action="/lowiska" method="GET" class="space-y-6">

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Miejscowość</label>
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
                            <input type="text" name="miejscowosc" value="{{ request('miejscowosc') }}" placeholder="Szukaj po miejscowości..." class="w-full pl-10 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Typ łowiska</label>
                        <select name="typ" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none cursor-pointer">
                            <option value="Wszystkie" {{ request('typ') == 'Wszystkie' ? 'selected' : '' }}>Wszystkie</option>
                            <option value="jezioro" {{ request('typ') == 'jezioro' ? 'selected' : '' }}>Jezioro</option>
                            <option value="rzeka" {{ request('typ') == 'rzeka' ? 'selected' : '' }}>Rzeka</option>
                            <option value="zalew" {{ request('typ') == 'zalew' ? 'selected' : '' }}>Zalew</option>
                        </select>
                    </div>

                    <div class="relative" id="custom-gatunek-select">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Gatunek ryby</label>

                        <input type="hidden" name="gatunek" id="gatunek-input" value="{{ request('gatunek', 'Wszystkie') }}">

                        <button type="button" id="gatunek-button" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none flex justify-between items-center transition hover:bg-gray-100">
                            <span id="gatunek-display" class="truncate font-medium text-gray-700">{{ request('gatunek', 'Wszystkie') }}</span>
                            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-200" id="gatunek-arrow"></i>
                        </button>

                        <ul id="gatunek-dropdown" class="hidden absolute z-50 w-full bg-white border border-gray-200 rounded-lg mt-1 shadow-lg max-h-56 overflow-y-auto custom-scrollbar top-full left-0">
                            <li class="px-3 py-2 hover:bg-emerald-50 hover:text-emerald-700 cursor-pointer text-sm transition font-medium" data-value="Wszystkie">Wszystkie</li>
                            @if(isset($allFishes))
                                @foreach($allFishes as $fish)
                                    <li class="px-3 py-2 hover:bg-emerald-50 hover:text-emerald-700 cursor-pointer text-sm transition" data-value="{{ $fish->name }}">
                                        {{ $fish->name }}
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Ocena minimalna</label>

                        <input type="hidden" name="ocena" id="ocena-input" value="{{ request('ocena', 'Wszystkie') }}">

                        <div class="flex flex-col">
                            <div class="flex items-center space-x-1 cursor-pointer no-select" id="star-rating-container">
                                @for($i = 1; $i <= 5; $i++)
                                    <div class="star-wrapper text-2xl text-gray-300 hover:scale-110 transition-transform px-0.5" data-index="{{ $i }}">
                                        <i class="fa-regular fa-star pointer-events-none"></i>
                                    </div>
                                @endfor
                                <span id="ocena-display" class="ml-3 text-sm font-bold text-emerald-700 bg-emerald-50 px-2 py-1 rounded-md border border-emerald-200 min-w-[3rem] text-center shadow-sm">
                                    {{ request('ocena') != 'Wszystkie' && request('ocena') ? request('ocena') . '+' : 'Brak' }}
                                </span>
                            </div>

                            <button type="button" id="clear-ocena-btn" class="text-xs text-red-500 hover:text-red-700 mt-2 self-start transition font-medium {{ request('ocena') != 'Wszystkie' && request('ocena') ? '' : 'hidden' }}">
                                <i class="fa-solid fa-xmark mr-1"></i> Wyczyść ocenę
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-2.5 rounded-lg transition shadow-md mt-2">
                        Zastosuj filtry
                    </button>

                    @if(request()->has('miejscowosc') || request()->has('typ') || request()->has('gatunek') || request()->has('ocena'))
                        <a href="/lowiska" class="block text-center text-sm text-gray-500 hover:text-red-500 mt-2 transition">Wyczyść wszystkie filtry</a>
                    @endif
                </form>
            </div>
        </aside>

        <section class="w-full md:w-3/4 relative z-10">

            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Znaleziono {{ $spots->total() }} łowisk</h1>

                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Sortuj:</span>
                    <select class="bg-white border border-gray-200 text-sm rounded-lg px-3 py-1.5 focus:outline-none cursor-pointer">
                        <option>Alfabetycznie</option>
                        <option>Najwyżej oceniane</option>
                    </select>
                </div>
            </div>

            <div class="space-y-5">
                @forelse($spots as $spot)
                    @php
                        $avgRating = $spot->reviews->count() > 0 ? number_format($spot->reviews->avg('rating'), 1) : '0.0';
                    @endphp

                    <div onclick="window.location='{{ url('/lowiska/' . $spot->id . '-' . Str::slug($spot->name)) }}'" class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col sm:flex-row overflow-hidden hover:shadow-md transition group cursor-pointer">

                        <div class="sm:w-1/3 relative min-h-[200px] sm:min-h-full overflow-hidden">
                            <img src="{{ $spot->main_image_url }}" alt="{{ $spot->name }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute top-3 right-3 bg-white text-gray-800 text-xs font-bold px-3 py-1 rounded-md shadow-md uppercase tracking-wider">
                                {{ $spot->type }}
                            </div>
                        </div>

                        <div class="p-5 sm:w-2/3 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-lg font-bold text-gray-800 hover:text-emerald-700 transition">
                                        <a href="/lowiska/{{ $spot->id }}-{{ Str::slug($spot->name) }}" onclick="event.stopPropagation()">{{ $spot->name }}</a>
                                    </h3>
                                    <div class="flex items-center gap-2">
                                        @auth
                                            @if(Auth::user()->isAdmin())
                                                <form action="{{ route('admin.spots.destroy', $spot->id) }}" method="POST" class="admin-confirm-form inline" data-title="Usuwanie łowiska" data-text="Czy na pewno chcesz bezpowrotnie usunąć łowisko '{{ $spot->name }}' wraz ze wszystkimi przypisanymi opiniami i rekordami ryb?" data-confirm-text="Usuń łowisko" data-type="danger" onclick="event.stopPropagation()">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 w-8 h-8 flex items-center justify-center rounded-lg transition shadow-sm hover:-translate-y-0.5" title="Usuń łowisko">
                                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                                <p class="text-sm text-emerald-600 font-semibold mb-3"><i class="fa-solid fa-location-dot mr-1"></i> {{ $spot->city }}, {{ $spot->region }}</p>
                                <p class="text-gray-600 text-sm line-clamp-2">{{ $spot->description }}</p>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap justify-between items-center gap-2">
                                <div class="flex space-x-4 text-sm text-gray-500">
                                    <span title="Gatunki ryb"><i class="fa-solid fa-fish text-gray-400 mr-1"></i> {{ count($spot->fish_species) }} gatunków ryb</span>
                                    <span title="Ilość opinii"><i class="fa-regular fa-comments text-gray-400 mr-1"></i> {{ $spot->reviews->count() }} opinii</span>
                                </div>

                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center bg-gray-50 px-2 py-1 rounded border border-gray-100">
                                        <i class="fa-solid fa-star {{ $spot->reviews->count() > 0 ? 'text-amber-500' : 'text-gray-300' }} mr-1"></i>
                                        <span class="font-bold text-gray-800">{{ $avgRating }}</span>
                                    </div>
                                    @auth
                                        @php
                                            $isFavorite = auth()->user()->favorite_spots->contains($spot->id);
                                        @endphp
                                        <button onclick="event.stopPropagation(); toggleFavorite(this, {{ $spot->id }})" class="{{ $isFavorite ? 'text-red-500' : 'text-gray-400' }} hover:text-red-500 transition text-xl focus:outline-none" data-favorite="{{ $isFavorite ? 'true' : 'false' }}" title="{{ $isFavorite ? 'Usuń z ulubionych' : 'Dodaj do ulubionych' }}">
                                            <i class="{{ $isFavorite ? 'fa-solid' : 'fa-regular' }} fa-heart transition-all duration-300 transform hover:scale-110"></i>
                                        </button>
                                    @else
                                        <button onclick="event.stopPropagation(); openLoginModal()" class="text-gray-400 hover:text-red-500 transition text-xl focus:outline-none" title="Zaloguj się, aby dodać do ulubionych">
                                            <i class="fa-regular fa-heart transition-all duration-300 transform hover:scale-110"></i>
                                        </button>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-10 text-center">
                        <i class="fa-solid fa-fish-crying text-4xl text-gray-300 mb-3"></i>
                        <h3 class="text-xl font-bold text-gray-600">Brak wyników</h3>
                        <p class="text-gray-400 mt-2">Nie znaleźliśmy łowisk spełniających Twoje kryteria.</p>
                        <a href="/lowiska" class="mt-4 inline-block text-emerald-600 font-semibold hover:underline transition">Wyczyść wszystkie filtry</a>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $spots->appends(request()->query())->links('partials.pagination') }}
            </div>

        </section>
    </main>

    <footer class="bg-emerald-950 text-emerald-200 py-6 mt-auto relative z-40">
        <div class="container mx-auto px-4 text-center">
            <div class="flex items-center justify-center space-x-2 text-white mb-2">
                <i class="fa-solid fa-fish-fins text-xl text-amber-500"></i>
                <h4 class="text-lg font-bold">Wędkarski Radar</h4>
            </div>
            <p class="text-sm opacity-80 mb-2">Projekt aplikacji internetowej zrealizowany na potrzeby zajęć akademickich.</p>
            <div class="text-xs opacity-60">
                &copy; 2026 Wędkarski Radar. Wszelkie prawa zastrzeżone.
            </div>
        </div>
    </footer>

    <div id="login-modal" class="fixed inset-0 z-[9999] hidden opacity-0 transition-all duration-300 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-emerald-950/70 backdrop-blur-md cursor-pointer" onclick="closeLoginModal()"></div>

    <div class="relative z-10 w-full max-w-[400px] transform scale-95 transition-all duration-300 flex flex-col items-center" id="login-card">
        <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden w-full">

            <div class="bg-emerald-900 pt-10 pb-6 text-center relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-800 rounded-full opacity-30"></div>
                <div class="absolute top-10 -left-10 w-20 h-20 bg-emerald-950 rounded-full opacity-30"></div>

                <div class="relative z-10">
                    <i class="fa-solid fa-fish text-4xl text-amber-400 drop-shadow-md mb-2"></i>
                    <h2 class="text-2xl font-bold text-white tracking-tight">Wędkarski Radar</h2>
                    <p class="text-emerald-200 text-sm font-medium opacity-90 mt-1">Witaj z powrotem na łowisku!</p>
                </div>

                <div class="flex justify-center space-x-8 mt-6 relative z-10">
                    <button onclick="switchTab('login')" id="tab-login" class="text-white text-xs font-black uppercase tracking-widest border-b-4 border-amber-400 pb-2 transition-all">Logowanie</button>
                    <button onclick="switchTab('register')" id="tab-register" class="text-emerald-300 hover:text-white text-xs font-black uppercase tracking-widest border-b-4 border-transparent pb-2 transition-all">Rejestracja</button>
                </div>
            </div>

            <div class="p-10">
                <form id="form-login" method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Adres E-mail</label>
                        <div class="relative">
                            <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
                            <input type="email" name="email" placeholder="np. jan.kowalski@email.com" required
                                class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border-2 border-emerald-500/20 rounded-2xl focus:border-emerald-500 focus:ring-0 outline-none transition-all text-sm placeholder:text-gray-300">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <div class="flex justify-between items-center px-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Hasło</label>

                        </div>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
                            <input type="password" name="password" placeholder="••••••••" required
                                class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border-2 border-emerald-500/10 rounded-2xl focus:border-emerald-500 focus:ring-0 outline-none transition-all text-sm placeholder:text-gray-300">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-black py-4 rounded-2xl shadow-xl transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2 tracking-widest text-sm">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> ZALOGUJ SIĘ
                    </button>

                    <div class="mt-8 text-center" id="switch-to-register">
                        <p class="text-xs text-gray-400 font-bold">
                            Nie masz jeszcze konta?
                            <button type="button" onclick="switchTab('register')" class="text-amber-500 hover:text-amber-600 ml-1">Dołącz do nas!</button>
                        </p>
                    </div>
                </form>

                <form id="form-register" method="POST" action="{{ route('register') }}" class="space-y-5 hidden">
                    @csrf
                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Imię i Nazwisko</label>
                        <input type="text" name="name" required class="w-full px-4 py-3.5 bg-gray-50 border-2 border-emerald-500/10 rounded-2xl focus:border-emerald-500 outline-none text-sm">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Adres E-mail</label>
                        <input type="email" name="email" required class="w-full px-4 py-3.5 bg-gray-50 border-2 border-emerald-500/10 rounded-2xl focus:border-emerald-500 outline-none text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-left">
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Hasło</label>
                            <input type="password" name="password" required class="w-full px-4 py-3 bg-gray-50 border-2 border-emerald-500/10 rounded-2xl focus:border-emerald-500 outline-none text-sm">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Powtórz</label>
                            <input type="password" name="password_confirmation" required class="w-full px-4 py-3 bg-gray-50 border-2 border-emerald-500/10 rounded-2xl focus:border-emerald-500 outline-none text-sm">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-black py-4 rounded-2xl shadow-xl transition-all transform hover:-translate-y-1 tracking-widest text-sm mt-2">
                        DOŁĄCZ DO NAS!
                    </button>
                    <div class="mt-4 text-center">
                        <button type="button" onclick="switchTab('login')" class="text-xs text-gray-400 font-bold hover:text-emerald-600 transition-colors">Masz już konto? Zaloguj się</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-6 w-full flex justify-center">
            <button onclick="closeLoginModal()" class="flex items-center text-emerald-100/80 hover:text-white transition-colors text-xs font-bold gap-2 drop-shadow-md">
                <i class="fa-solid fa-arrow-left"></i> Powrót na stronę główną
            </button>
        </div>
    </div>
</div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Oceny w formie gwiazdek z możliwością wyboru pół gwiazdki
            const starContainer = document.getElementById('star-rating-container');
            const stars = starContainer.querySelectorAll('.star-wrapper');
            const hiddenOcenaInput = document.getElementById('ocena-input');
            const ocenaDisplay = document.getElementById('ocena-display');
            const clearOcenaBtn = document.getElementById('clear-ocena-btn');

            let currentRatingValue = parseFloat(hiddenOcenaInput.value) || 0;

            function renderStars(value) {
                stars.forEach((star, index) => {
                    const starIndex = index + 1;
                    const icon = star.querySelector('i');

                    icon.className = 'fa-star pointer-events-none transition-colors duration-150';

                    if (value >= starIndex) {
                        icon.classList.add('fa-solid', 'text-amber-500');
                    } else if (value === starIndex - 0.5) {
                        icon.classList.add('fa-solid', 'fa-star-half-stroke', 'text-amber-500');
                    } else {
                        icon.classList.add('fa-regular', 'text-gray-300');
                    }
                });
            }

            renderStars(currentRatingValue);

            stars.forEach(star => {
                star.addEventListener('mousemove', function(e) {
                    const rect = this.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const isHalf = x < rect.width / 2;
                    const starVal = parseFloat(this.getAttribute('data-index'));
                    const hoverValue = isHalf ? starVal - 0.5 : starVal;

                    renderStars(hoverValue);
                });

                star.addEventListener('click', function(e) {
                    const rect = this.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const isHalf = x < rect.width / 2;
                    const starVal = parseFloat(this.getAttribute('data-index'));

                    currentRatingValue = isHalf ? starVal - 0.5 : starVal;

                    hiddenOcenaInput.value = currentRatingValue;
                    ocenaDisplay.textContent = currentRatingValue + '+';
                    clearOcenaBtn.classList.remove('hidden');
                    renderStars(currentRatingValue);
                });
            });

            starContainer.addEventListener('mouseleave', function() {
                renderStars(currentRatingValue);
            });

            clearOcenaBtn.addEventListener('click', function() {
                currentRatingValue = 0;
                hiddenOcenaInput.value = 'Wszystkie';
                ocenaDisplay.textContent = 'Brak';
                clearOcenaBtn.classList.add('hidden');
                renderStars(0);
            });


            // dropdown do wyboru gatunku ryby
            const gatunekBtn = document.getElementById('gatunek-button');
            const gatunekDropdown = document.getElementById('gatunek-dropdown');
            const gatunekInput = document.getElementById('gatunek-input');
            const gatunekDisplay = document.getElementById('gatunek-display');
            const gatunekArrow = document.getElementById('gatunek-arrow');

            // Otwieranie i zamykanie
            gatunekBtn.addEventListener('click', function(e) {
                e.preventDefault();
                gatunekDropdown.classList.toggle('hidden');
                gatunekArrow.classList.toggle('rotate-180');
            });

            // Wybór elementu z listy
            const gatunekOptions = gatunekDropdown.querySelectorAll('li');
            gatunekOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const selectedValue = this.getAttribute('data-value');

                    gatunekInput.value = selectedValue;
                    gatunekDisplay.textContent = selectedValue;

                    gatunekDropdown.classList.add('hidden');
                    gatunekArrow.classList.remove('rotate-180');
                });
            });

            // Zamknięcie przy kliknięciu poza listą
            document.addEventListener('click', function(e) {
                if (!document.getElementById('custom-gatunek-select').contains(e.target)) {
                    gatunekDropdown.classList.add('hidden');
                    gatunekArrow.classList.remove('rotate-180');
                }
            });

        });

                window.openLoginModal = function() {
        const modal = document.getElementById('login-modal');
        const card = document.getElementById('login-card');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            card.classList.replace('scale-95', 'scale-100');
        }, 20);
        document.body.style.overflow = 'hidden';
    };

    window.closeLoginModal = function() {
        const modal = document.getElementById('login-modal');
        const card = document.getElementById('login-card');
        modal.classList.add('opacity-0');
        card.classList.replace('scale-100', 'scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 300);
    };

    function switchTab(type) {
        const loginForm = document.getElementById('form-login');
        const regForm = document.getElementById('form-register');
        const loginTab = document.getElementById('tab-login');
        const regTab = document.getElementById('tab-register');

        if (type === 'login') {
            loginForm.classList.remove('hidden');
            regForm.classList.add('hidden');
            loginTab.classList.replace('text-emerald-300', 'text-white');
            loginTab.classList.replace('border-transparent', 'border-amber-400');
            regTab.classList.replace('text-white', 'text-emerald-300');
            regTab.classList.replace('border-amber-400', 'border-transparent');
        } else {
            loginForm.classList.add('hidden');
            regForm.classList.remove('hidden');
            regTab.classList.replace('text-emerald-300', 'text-white');
            regTab.classList.replace('border-transparent', 'border-amber-400');
            loginTab.classList.replace('text-white', 'text-emerald-300');
            loginTab.classList.replace('border-amber-400', 'border-transparent');
        }
    }

    @if($errors->any())
        window.addEventListener('load', () => {
            setTimeout(window.openLoginModal, 500);
        });
    @endif

    // Funkcja AJAX (Fetch API) odpowiedzialna za przełączanie stanu ulubionego łowiska.
    // Metoda wysyła asynchroniczne żądanie POST, przekazując token bezpieczeństwa CSRF,
    // a po pomyślnym zapisie w bazie danych dynamicznie modyfikuje klasy CSS ikony serca,
    // wyzwalając jednocześnie mikro-animację powiększenia (pulsowania).
    function toggleFavorite(btn, spotId) {
        const icon = btn.querySelector('i');

        // Wysyłamy żądanie POST do endpointu Laravel.
        // Konieczne jest przekazanie nagłówka X-CSRF-TOKEN z aktualnym tokenem sesyjnym
        // w celu uwierzytelnienia żądania i zapobieżenia atakom CSRF (Cross-Site Request Forgery).
        fetch(`/lowiska/${spotId}/toggle-favorite`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.is_favorite) {
                    // Stan: Łowisko zostało dodane do ulubionych.
                    // Podmieniamy klasę FontAwesome na pełne serce (fa-solid) i zmieniamy kolor na czerwony.
                    icon.classList.replace('fa-regular', 'fa-solid');
                    btn.classList.remove('text-gray-400');
                    btn.classList.add('text-red-500');
                    btn.setAttribute('title', 'Usuń z ulubionych');
                    
                    // Mikro-animacja: Powiększamy ikonę za pomocą transformacji scale-125 na okres 200ms
                    icon.classList.add('scale-125');
                    setTimeout(() => icon.classList.remove('scale-125'), 200);
                } else {
                    // Stan: Łowisko zostało usunięte z ulubionych.
                    // Przywracamy obrys serca (fa-regular) i neutralny szary kolor (text-gray-400).
                    icon.classList.replace('fa-solid', 'fa-regular');
                    btn.classList.remove('text-red-500');
                    btn.classList.add('text-gray-400');
                    btn.setAttribute('title', 'Dodaj do ulubionych');
                }
            }
        })
        .catch(error => {
            console.error('Błąd podczas zapisywania ulubionych:', error);
        });
    }

    // Płynna nawigacja przy przewijaniu strony
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('main-navbar');
        const container = document.getElementById('main-navbar-container');
        if (navbar && container) {
            if (window.scrollY > 20) {
                navbar.classList.replace('bg-emerald-900/95', 'bg-emerald-950/90');
                navbar.classList.add('shadow-lg');
                container.classList.replace('py-4', 'py-2.5');
            } else {
                navbar.classList.replace('bg-emerald-950/90', 'bg-emerald-900/95');
                navbar.classList.remove('shadow-lg');
                container.classList.replace('py-2.5', 'py-4');
            }
        }
    });

    </script>
    <!-- Beautiful Custom Confirmation Modal -->
    <div id="confirm-modal" class="fixed inset-0 z-[10000] hidden opacity-0 transition-all duration-300 flex items-center justify-center p-4">
        <!-- Backdrop with glassmorphism blur -->
        <div class="absolute inset-0 bg-emerald-950/60 backdrop-blur-sm cursor-pointer" onclick="closeConfirmModal()"></div>

        <!-- Modal Card -->
        <div class="relative z-10 w-full max-w-[440px] transform scale-95 transition-all duration-300 bg-white rounded-[2rem] shadow-2xl overflow-hidden p-6 border border-emerald-500/10">
            <!-- Decorative Accent background gradient -->
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-red-500 via-amber-500 to-emerald-500" id="confirm-modal-bar"></div>

            <div class="flex items-start space-x-4 mt-2">
                <!-- Icon with tailored background -->
                <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center shrink-0 shadow-inner" id="confirm-modal-icon-bg">
                    <i class="fa-solid fa-trash-can text-lg" id="confirm-modal-icon"></i>
                </div>
                
                <div class="flex-grow">
                    <!-- Title & Subtitle -->
                    <h3 class="text-lg font-black text-gray-900 tracking-tight" id="confirm-modal-title">Potwierdź usunięcie</h3>
                    <p class="text-[9px] text-gray-400 font-extrabold uppercase tracking-widest mt-0.5">Wymagana autoryzacja administratora</p>
                    
                    <!-- Content text -->
                    <p class="text-sm text-gray-600 mt-3 leading-relaxed" id="confirm-modal-text">Czy na pewno chcesz usunąć tę opinię? Tej operacji nie będzie można cofnąć.</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-3 justify-end mt-6 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeConfirmModal()" class="px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold rounded-xl text-xs transition duration-200 focus:outline-none border border-gray-100">
                    Anuluj
                </button>
                <button type="button" id="confirm-modal-btn" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-xs transition duration-200 focus:outline-none shadow-md shadow-red-500/20 hover:-translate-y-0.5 transform">
                    Potwierdź
                </button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const confirmModal = document.getElementById('confirm-modal');
        if (confirmModal) {
            const confirmTitle = document.getElementById('confirm-modal-title');
            const confirmText = document.getElementById('confirm-modal-text');
            const confirmBtn = document.getElementById('confirm-modal-btn');
            const confirmIcon = document.getElementById('confirm-modal-icon');
            const confirmIconBg = document.getElementById('confirm-modal-icon-bg');
            const confirmBar = document.getElementById('confirm-modal-bar');
            
            window.closeConfirmModal = function() {
                confirmModal.classList.add('opacity-0');
                confirmModal.querySelector('.relative').classList.add('scale-95');
                setTimeout(() => {
                    confirmModal.classList.add('hidden');
                }, 300);
            };

            window.openConfirmModal = function(title, text, confirmLabel, type, onConfirm) {
                confirmTitle.textContent = title;
                confirmText.textContent = text;
                confirmBtn.textContent = confirmLabel;
                
                // Konfiguracja kolorystyki i ikon pod typ akcji
                if (type === 'danger') {
                    confirmBar.className = "absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-red-500 to-amber-500";
                    confirmIconBg.className = "w-12 h-12 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center shrink-0 shadow-inner";
                    confirmIcon.className = "fa-solid fa-trash-can text-lg";
                    confirmBtn.className = "px-5 py-2 bg-red-600 hover:bg-red-750 text-white font-bold rounded-xl text-xs transition duration-200 focus:outline-none shadow-md shadow-red-500/20 hover:-translate-y-0.5 transform";
                } else if (type === 'warning') {
                    confirmBar.className = "absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-amber-500 to-yellow-500";
                    confirmIconBg.className = "w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0 shadow-inner";
                    confirmIcon.className = "fa-solid fa-triangle-exclamation text-lg";
                    confirmBtn.className = "px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl text-xs transition duration-200 focus:outline-none shadow-md shadow-amber-500/20 hover:-translate-y-0.5 transform";
                } else { // success / info
                    confirmBar.className = "absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 to-teal-500";
                    confirmIconBg.className = "w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 shadow-inner";
                    confirmIcon.className = "fa-solid fa-circle-check text-lg";
                    confirmBtn.className = "px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition duration-200 focus:outline-none shadow-md shadow-emerald-500/20 hover:-translate-y-0.5 transform";
                }

                confirmBtn.onclick = function() {
                    onConfirm();
                    closeConfirmModal();
                };

                confirmModal.classList.remove('hidden');
                setTimeout(() => {
                    confirmModal.classList.remove('opacity-0');
                    confirmModal.querySelector('.relative').classList.remove('scale-95');
                }, 10);
            };

            // Nasłuchiwanie na wysyłanie formularzy z klasą admin-confirm-form
            document.body.addEventListener('submit', function(e) {
                const form = e.target.closest('.admin-confirm-form');
                if (form && !form.dataset.confirmed) {
                    e.preventDefault();
                    const title = form.getAttribute('data-title') || 'Wymagane potwierdzenie';
                    const text = form.getAttribute('data-text') || 'Czy na pewno chcesz to zrobić?';
                    const confirmLabel = form.getAttribute('data-confirm-text') || 'Potwierdź';
                    const type = form.getAttribute('data-type') || 'danger';
                    
                    openConfirmModal(title, text, confirmLabel, type, function() {
                        form.dataset.confirmed = "true";
                        form.submit();
                    });
                }
            });
        }
    });

    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    }
    </script>
</body>
</html>
