<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Wędkarski Radar - Baza Wędkarska</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2">
    <link rel="apple-touch-icon" href="/favicon.svg?v=2">
    <style>
        /* Styl dla gradientowego wypełnienia tła wykresu aktywności brań */
        .chart-bg {
            background: linear-gradient(to top, rgba(16, 185, 129, 0.2), transparent);
            clip-path: polygon(0% 100%, 0% 70%, 20% 75%, 40% 70%, 60% 85%, 80% 60%, 100% 50%, 100% 100%);
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800 flex flex-col min-h-screen">

    <!-- ==========================================
         SEKCJA 1: NAWIGACJA GŁÓWNA
         ========================================== -->
    <nav id="main-navbar" class="sticky top-0 z-50 bg-emerald-900/95 backdrop-blur-md text-white shadow-md transition-all duration-300">
        <div id="main-navbar-container" class="container mx-auto flex flex-wrap justify-between items-center px-4 py-4 transition-all duration-300">
            <div class="flex items-center justify-between w-full md:w-auto">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-fish-fins text-2xl text-amber-400"></i>
                    <h1 class="text-xl font-bold tracking-tight">Wędkarski Radar</h1>
                </div>
                <button type="button" onclick="toggleMobileMenu()" class="md:hidden text-emerald-200 hover:text-white focus:outline-none">
                    <i class="fa-solid fa-bars text-2xl"></i>
                </button>
            </div>
            
            <div id="mobile-menu" class="hidden md:flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-6 font-medium text-sm w-full md:w-auto mt-4 md:mt-0 pb-4 md:pb-0 transition-all duration-300">
                <a href="/lowiska" class="hover:text-amber-400 transition flex items-center">
                    <i class="fa-solid fa-water mr-2"></i> Wszystkie łowiska
                </a>
                <a href="/mapa" class="hover:text-amber-400 transition flex items-center">
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

    <!-- ==========================================
         SEKCJA 2: NAGŁÓWEK GŁÓWNY
         ========================================== -->
    <header class="relative bg-black h-[500px] flex items-center justify-center text-center">
        <div class="absolute inset-0 w-full h-full">
            <img src="{{ asset('images/baner.jpg') }}" alt="Baner" class="w-full h-full object-cover opacity-60">
        </div>

        <div class="relative z-10 px-4 max-w-3xl">
            <h2 class="text-4xl md:text-5xl font-extrabold text-white mb-4 drop-shadow-lg">Najlepsze łowiska w Polsce</h2>
            <p class="text-lg md:text-xl text-gray-200 mb-8 drop-shadow-md">Odkryj sprawdzone miejsca, sprawdź warunki łowienia i podziel się swoimi doświadczeniami</p>
            <a href="/lowiska" class="inline-block bg-amber-500 hover:bg-amber-600 text-white font-bold text-lg px-8 py-4 rounded-lg shadow-lg transition transform hover:-translate-y-1">
                Wszystkie łowiska <i class="fa-solid fa-arrow-right ml-2"></i>
            </a>
        </div>
    </header>

    <!-- ==========================================
         SEKCJA 3: GŁÓWNA TREŚĆ STRONY
         ========================================== -->
    <main class="container mx-auto px-4 py-10 flex-grow max-w-6xl">

        <!-- MODUŁ LIVE: Wskaźnik i Prognoza Warunków Łowienia -->
        <div class="bg-white rounded-3xl shadow-xl border border-emerald-100 p-6 mb-10 overflow-hidden italic">
            <!-- Nagłówek panelu z lokalizacją -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h3 class="text-2xl font-black text-gray-900 uppercase tracking-tight flex items-center gap-2"><i class="fa-solid fa-square-poll-vertical text-emerald-600"></i> Warunki łowienia (Live)</h3>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mt-1 flex items-center gap-1.5" id="home-live-date-label">
                        <i class="fa-regular fa-clock text-emerald-500"></i> <span id="home-live-time">Wczytywanie...</span>
                    </p>
                </div>
                
                <div class="flex flex-wrap md:flex-nowrap items-center gap-3 w-full md:w-auto">
                    <!-- Informacja o źródle danych -->
                    <div class="p-2 bg-emerald-50 rounded-xl border border-emerald-100 flex items-center space-x-2.5 flex-wrap gap-y-2">
                        <i class="fa-solid fa-location-crosshairs text-emerald-700 text-sm animate-pulse" id="home-loc-icon"></i>
                        <span class="text-xs font-black text-emerald-950 uppercase" id="home-loc-name">Domyślna (Warszawa)</span>
                        <button type="button" onclick="toggleHomeLocationSource()" class="bg-white hover:bg-emerald-100 text-emerald-800 font-bold text-[10px] px-2.5 py-1.5 rounded-lg border border-emerald-200 transition-all uppercase tracking-wider shadow-sm">
                            Zmień na GPS
                        </button>
                    </div>

                    <!-- Wskaźnik brań -->
                    <div class="flex items-center space-x-2">
                        <span id="home-bite-score" class="bg-gray-100 text-gray-400 font-black px-4 py-2 rounded-2xl shadow-md text-base transition-all duration-500">--/100</span>
                        <span id="home-bite-label" class="text-gray-400 text-xs font-black uppercase tracking-widest">Ładowanie</span>
                    </div>
                </div>
            </div>

            <!-- Trzy paski punktowe wskaźników -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-emerald-50/20 p-3 rounded-2xl border border-emerald-100/50 flex justify-between items-center text-xs font-bold">
                    <span class="text-gray-600">Ciśnienie atmosferyczne: <span class="text-blue-600" id="home-pts-pressure">--/10 pkt</span></span>
                    <div class="w-24 h-2 bg-gray-100 rounded-full overflow-hidden shadow-inner">
                        <div id="home-bar-pressure" class="bg-blue-500 h-full rounded-full transition-all duration-700" style="width: 0%"></div>
                    </div>
                </div>
                <div class="bg-emerald-50/20 p-3 rounded-2xl border border-emerald-100/50 flex justify-between items-center text-xs font-bold">
                    <span class="text-gray-600">Wpływ księżyca: <span class="text-purple-600" id="home-pts-moon">--/10 pkt</span></span>
                    <div class="w-24 h-2 bg-gray-100 rounded-full overflow-hidden shadow-inner">
                        <div id="home-bar-moon" class="bg-purple-500 h-full rounded-full transition-all duration-700" style="width: 0%"></div>
                    </div>
                </div>
                <div class="bg-emerald-50/20 p-3 rounded-2xl border border-emerald-100/50 flex justify-between items-center text-xs font-bold">
                    <span class="text-gray-600">Pogoda i chmury: <span class="text-emerald-600" id="home-pts-weather">--/10 pkt</span></span>
                    <div class="w-24 h-2 bg-gray-100 rounded-full overflow-hidden shadow-inner">
                        <div id="home-bar-weather" class="bg-emerald-500 h-full rounded-full transition-all duration-700" style="width: 0%"></div>
                    </div>
                </div>
            </div>

            <!-- Trzy kolumny szczegółowe -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Faza księżyca -->
                <div class="bg-emerald-50/10 rounded-2xl p-5 border border-gray-100 flex flex-col justify-between h-40">
                    <div>
                        <div class="flex items-center space-x-2 text-gray-800 font-bold mb-2">
                            <i class="fa-solid fa-moon text-purple-600"></i> <span class="text-xs uppercase tracking-wider">Faza księżyca</span>
                        </div>
                        <p class="text-sm font-bold text-gray-800" id="home-moon-name">Obliczanie...</p>
                        <p class="text-xs text-gray-400 mb-2" id="home-moon-desc">Analizowanie orbity...</p>
                    </div>
                    <div class="flex justify-between items-end">
                        <span id="home-moon-rating" class="text-xs font-black uppercase tracking-widest text-purple-600">Wczytywanie...</span>
                        <div class="text-3xl text-purple-600" id="home-moon-icon"><i class="fa-solid fa-circle-notch animate-spin text-lg"></i></div>
                    </div>
                </div>

                <!-- Ciśnienie -->
                <div class="bg-emerald-50/10 rounded-2xl p-5 border border-gray-100 flex flex-col justify-between h-40">
                    <div>
                        <div class="flex items-center space-x-2 text-gray-800 font-bold mb-2">
                            <i class="fa-solid fa-gauge-high text-blue-600"></i> <span class="text-xs uppercase tracking-wider">Ciśnienie (Live)</span>
                        </div>
                        <div class="flex items-baseline space-x-1">
                            <span class="text-3xl font-extrabold text-gray-900" id="home-pressure-val">--</span> <span class="text-xs text-gray-400 font-bold">hPa</span>
                        </div>
                        <p class="text-[11px] font-bold text-red-500 mt-1" id="home-pressure-trend">Ładowanie...</p>
                    </div>
                    <div>
                        <span id="home-pressure-rating" class="text-xs font-black uppercase tracking-widest text-blue-600">Wczytywanie...</span>
                    </div>
                </div>

                <!-- Pogoda i Temp -->
                <div class="bg-emerald-50/10 rounded-2xl p-5 border border-gray-100 flex flex-col justify-between h-40">
                    <div>
                        <div class="flex items-center space-x-2 text-gray-800 font-bold mb-2">
                            <i class="fa-solid fa-cloud text-emerald-600"></i> <span class="text-xs uppercase tracking-wider">Warunki pogodowe</span>
                        </div>
                        <div class="flex items-baseline space-x-2">
                            <span id="home-weather-icon" class="text-2xl text-amber-500"><i class="fa-solid fa-circle-notch animate-spin text-sm"></i></span>
                            <span class="text-3xl font-extrabold text-gray-900" id="home-weather-temp">--°C</span>
                        </div>
                        <p class="text-xs text-gray-600 font-bold mt-1" id="home-weather-name">Ładowanie...</p>
                    </div>
                    <div class="flex justify-between items-end">
                        <span id="home-weather-rating" class="text-xs font-black uppercase tracking-widest text-emerald-600">Wczytywanie...</span>
                        <p class="text-[10px] text-gray-400 font-bold" id="home-weather-desc">Wiatr: -- m/s</p>
                    </div>
                </div>
            </div>

            <!-- Dobowy wykres aktywności -->
            <div>
                <h4 class="font-black text-gray-900 text-sm uppercase tracking-widest mb-4 flex items-center gap-1.5"><i class="fa-solid fa-chart-line text-emerald-600"></i> Dobowy wykres brań</h4>
                <div class="bg-emerald-950 rounded-2xl p-4 relative overflow-hidden h-48 border border-emerald-900/50 flex flex-col justify-between">
                    <!-- Informacje w nagłówku wykresu -->
                    <div class="absolute top-4 left-4 text-white text-xs flex space-x-4 z-10 font-bold">
                        <span id="home-chart-temp-info">--°C <i class="fa-solid fa-cloud text-emerald-400"></i></span>
                        <span id="home-chart-press-info"><i class="fa-solid fa-gauge text-blue-400"></i> -- hPa</span>
                    </div>
                    <div class="absolute top-4 right-4 text-emerald-500/50 text-[10px] uppercase tracking-wider font-black z-10">Prognoza dobowej aktywności</div>

                    <!-- Kontener wykresu (bez paddingów dla idealnej zgodności wektora i nakładki HTML) -->
                    <div class="absolute top-12 bottom-7 left-4 right-4">
                        <!-- SVG Wykres -->
                        <svg class="w-full h-full" viewBox="0 0 100 30" preserveAspectRatio="none" id="home-chart-svg">
                            <defs>
                                <linearGradient id="home-chart-grad" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#10b981" stop-opacity="0.35"></stop>
                                    <stop offset="100%" stop-color="#10b981" stop-opacity="0.0"></stop>
                                </linearGradient>
                            </defs>
                            <path d="M 0,30 Q 50,30 100,30 L 100,30 L 0,30 Z" fill="url(#home-chart-grad)" id="home-chart-fill"></path>
                            <path d="M 0,30 Q 50,30 100,30" fill="none" stroke="#10b981" stroke-width="1.8" id="home-chart-line"></path>
                            <!-- Podświetlone przedziały dobrych brań (kolor żółty/amber) -->
                            <g id="home-chart-highlights"></g>
                        </svg>

                        <!-- Nakładka HTML na punkty i celownik (zapobiega deformacji proporcji) -->
                        <div class="absolute inset-0 pointer-events-none" id="home-chart-overlay">
                        </div>
                    </div>

                    <!-- Godziny pod wykresem -->
                    <div class="absolute bottom-1.5 left-0 w-full px-1 md:px-4 flex justify-between text-emerald-400/60 text-[7px] md:text-[9px] font-black font-mono tracking-widest overflow-hidden">
                        <span>00:00</span>
                        <span>03:00</span>
                        <span>06:00</span>
                        <span>09:00</span>
                        <span>12:00</span>
                        <span>15:00</span>
                        <span>18:00</span>
                        <span>21:00</span>
                        <span>23:59</span>
                    </div>
                </div>

                <div class="bg-emerald-50/40 border border-emerald-100 p-3 rounded-2xl mt-4 text-xs font-bold text-center">
                    <span class="text-emerald-800 uppercase tracking-wider"><i class="fa-solid fa-award mr-1"></i> Najlepsze godziny na łowienie dzisiaj:</span> 
                    <span class="text-emerald-950 font-black tracking-wide" id="home-golden-hours">obliczanie...</span>
                </div>
            </div>
        </div>

        <!-- ==========================================
             SEKCJA 4: PANEL INTERAKTYWNEJ MAPY 
             ========================================== -->
        <section class="mb-16 relative rounded-3xl overflow-hidden shadow-2xl bg-emerald-950 border border-emerald-900/50">
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1524661135-423995f22d0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80')] bg-cover bg-center opacity-20 mix-blend-overlay"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-950 via-emerald-900/90 to-transparent"></div>

            <div class="relative z-10 p-8 md:p-12 lg:p-16 flex flex-col md:flex-row items-center justify-between">

                <div class="max-w-2xl text-white mb-10 md:mb-0">
                    <div class="inline-flex items-center space-x-2 bg-emerald-800/80 backdrop-blur-sm px-4 py-1.5 rounded-full text-emerald-200 text-sm font-bold mb-6 border border-emerald-700/50 shadow-inner">
                        <i class="fa-solid fa-satellite-dish animate-pulse text-amber-400"></i>
                        <span>Wyszukiwanie w promieniu</span>
                    </div>

                    <h2 class="text-3xl md:text-5xl font-extrabold mb-5 leading-tight text-transparent bg-clip-text bg-gradient-to-r from-white to-emerald-100">
                        Znajdź łowisko blisko domu
                    </h2>

                    <p class="text-emerald-100/90 text-lg mb-8 max-w-xl leading-relaxed">
                        Podaj swoją miejscowość, ustaw zasięg za pomocą suwaka <b class="text-amber-400">do 500 km</b> i od razu zobacz najlepsze akweny w Twojej okolicy na interaktywnej mapie.
                    </p>

                    <a href="/mapa" class="inline-flex items-center justify-center bg-amber-500 hover:bg-amber-400 text-emerald-950 font-black px-8 py-4 rounded-xl transition duration-300 transform hover:-translate-y-1 shadow-[0_0_20px_rgba(245,158,11,0.25)] hover:shadow-[0_0_25px_rgba(245,158,11,0.4)]">
                        <i class="fa-solid fa-map-location-dot mr-3 text-xl"></i> Otwórz Mapę Interaktywną
                    </a>
                </div>

                <div class="hidden md:flex relative w-64 h-64 justify-center items-center mr-8">
                    <div class="absolute inset-0 border-2 border-emerald-500/20 rounded-full animate-[ping_3s_linear_infinite]"></div>
                    <div class="absolute inset-6 border-2 border-emerald-500/30 rounded-full animate-[ping_3s_linear_infinite_0.75s]"></div>
                    <div class="absolute inset-12 border-[1.5px] border-emerald-500/40 rounded-full border-dashed animate-spin-slow" style="animation-duration: 15s;"></div>

                    <div class="relative bg-white text-emerald-700 w-16 h-16 rounded-full flex items-center justify-center shadow-[0_0_30px_rgba(16,185,129,0.5)] z-20">
                        <i class="fa-solid fa-location-crosshairs text-3xl"></i>
                    </div>

                    <div class="absolute top-8 left-12 bg-amber-500 w-4 h-4 rounded-full shadow-lg border border-white/20"></div>
                    <div class="absolute bottom-10 right-6 bg-amber-400 w-5 h-5 rounded-full shadow-lg border border-white/20"></div>
                    <div class="absolute top-24 right-4 bg-emerald-400 w-3 h-3 rounded-full shadow-lg border border-white/20"></div>
                </div>

            </div>
        </section>

        <!-- ==========================================
             SEKCJA 5: TOP 3 REKOMENDOWANE ŁOWISKA
             ========================================== -->
        <div class="text-center mb-8">
            <h3 class="text-3xl font-bold text-gray-800 flex items-center justify-center">
                <i class="fa-solid fa-trophy text-amber-500 mr-3"></i> Top 3 łowiska według ocen <i class="fa-solid fa-trophy text-amber-500 ml-3"></i>
            </h3>
            <p class="text-gray-500 mt-2">Najlepiej oceniane łowiska w Polsce</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            @foreach($recommendedSpots as $index => $spot)
                <div onclick="window.location='{{ url('/lowiska/' . $spot->id . '-' . Str::slug($spot->name)) }}'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition group cursor-pointer">
                    <div class="relative h-48">
                        <img src="{{ $spot->main_image_url }}" alt="{{ $spot->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">

                        <div class="absolute top-3 right-3 shadow-md rounded-full px-3 py-1 text-xs font-bold flex items-center
                            {{ $index == 0 ? 'bg-amber-500 text-white' : ($index == 1 ? 'bg-gray-300 text-gray-800' : 'bg-orange-600 text-white') }}">
                            <i class="fa-solid fa-medal mr-1"></i> {{ $index + 1 }}. MIEJSCE
                        </div>
                    </div>

                    <div class="p-5 flex flex-col flex-grow">
                        <h4 class="text-xl font-bold text-gray-900 mb-3 line-clamp-1">{{ $spot->name }}</h4>

                        <div class="mb-4 flex-grow">
                            <div class="space-y-2 text-sm text-gray-600">
                                <p class="line-clamp-1" title="{{ $spot->region }}, {{ $spot->city }}">
                                    <i class="fa-solid fa-location-dot w-4 text-gray-400"></i> {{ $spot->region }}, {{ $spot->city }}
                                </p>
                                <p><i class="fa-solid fa-fish w-4 text-gray-400"></i> {{ count($spot->fish_species) }} gatunków</p>
                            </div>
                        </div>

                        <hr class="border-gray-100 mb-3">

                        @php
                            $avgRating = $spot->reviews_avg_rating ? number_format($spot->reviews_avg_rating, 1) : '0.0';
                            $ratingVal = (float)$avgRating;
                        @endphp
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">{{ $spot->reviews->count() ?? 0 }} opinii</span>
                            <div class="flex items-center text-amber-500 text-xs">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $ratingVal)
                                        <i class="fa-solid fa-star"></i>
                                    @elseif($i - 0.5 <= $ratingVal)
                                        <i class="fa-solid fa-star-half-stroke"></i>
                                    @else
                                        <i class="fa-regular fa-star text-gray-300"></i>
                                    @endif
                                @endfor
                                <span class="text-gray-800 font-bold ml-1 text-sm">{{ $avgRating }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </main>

    <footer class="bg-emerald-950 text-emerald-200 py-6 mt-auto">
        <div class="container mx-auto px-4 text-center">
            <div class="flex items-center justify-center space-x-2 text-white mb-2">
                <i class="fa-solid fa-fish-fins text-xl text-amber-500"></i>
                <h4 class="text-lg font-bold">Wędkarski Radar</h4>
            </div>
            <p class="text-sm opacity-80 mb-2">Projekt aplikacji internetowej zrealizowany na potrzeby zajęć akademickich. Wykonał Kamil Kopczyk</p>
            <div class="text-xs opacity-60">
                &copy; 2026 Wędkarski Radar. Wszelkie prawa zastrzeżone.
            </div>
        </div>
    </footer>

    <!-- ==========================================
         SEKCJA 6: MODAL LOGOWANIA I REJESTRACJI
         ========================================== -->
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
    // === LIVE FORECAST SYGNALIZATOR BRAN (HOME DASHBOARD) ===
    const defaultLat = 52.2297;
    const defaultLng = 21.0122;
    let activeLat = defaultLat;
    let activeLng = defaultLng;
    let locationSource = 'default'; // 'default' or 'gps'

    // Obliczanie fazy księżyca na podstawie uproszczonego algorytmu astronomicznego.
    // Metoda przelicza datę kalendarzową na przybliżony czas juliański (Julian Date)
    // względem znanej epoki nowiu księżyca (694039.09 dnia). Synodyczny miesiąc trwa ok. 29.5305882 dni.
    function getMoonPhase(d) {
        let year = d.getFullYear();
        let month = d.getMonth() + 1;
        let day = d.getDate();
        
        // Korekta dla miesięcy zimowych (styczeń i luty traktujemy jako 13 i 14 miesiąc roku poprzedniego)
        if (month < 3) { year--; month += 12; }
        month++;
        
        // Obliczenie dni juliańskich z uwzględnieniem lat przestępnych
        let c = 365.25 * year;
        let e = 30.6 * month;
        let jd = c + e + day - 694039.09; // Przesunięcie o epoch nowiu
        jd /= 29.5305882;                 // Dzielenie przez długość cyklu synodycznego
        let b = parseInt(jd);             // Pobranie pełnych przebytych cykli
        jd -= b;                          // Część ułamkowa określa obecną pozycję w cyklu (od 0.0 do 1.0)
        
        // Rzutowanie pozycji (0-1) na 8 dyskretnych faz księżyca (od nowiu do ubywającego sierpa)
        let phaseIndex = Math.round(jd * 8);
        if (phaseIndex >= 8) phaseIndex = 0;
        
        const phases = [
            { name: "Nów", desc: "Znakomite warunki - ryby żerują", score: 95, icon: '<i class="fa-solid fa-circle text-purple-950"></i>' },
            { name: "Młody księżyc", desc: "Wysoka aktywność ryb drapieżnych", score: 75, icon: '<i class="fa-solid fa-moon text-purple-300"></i>' },
            { name: "Pierwsza kwadra", desc: "Umiarkowane brania", score: 60, icon: '<i class="fa-solid fa-circle-half-stroke text-purple-400"></i>' },
            { name: "Przyrastający garb", desc: "Osłabiona aktywność ryb", score: 45, icon: '<i class="fa-solid fa-moon text-purple-200"></i>' },
            { name: "Pełnia", desc: "Wzmożone brania nocne", score: 85, icon: '<i class="fa-solid fa-circle text-amber-100 drop-shadow-md"></i>' },
            { name: "Ubywający garb", desc: "Stabilne, średnie warunki", score: 55, icon: '<i class="fa-solid fa-moon text-purple-200"></i>' },
            { name: "Trzecia kwadra", desc: "Słabsze brania w ciągu dnia", score: 40, icon: '<i class="fa-solid fa-circle-half-stroke text-purple-400"></i>' },
            { name: "Ubywający sierp", desc: "Rosnąca aktywność", score: 70, icon: '<i class="fa-solid fa-moon text-purple-300"></i>' }
        ];
        return phases[phaseIndex];
    }

    const weatherCodes = {
        0: { desc: "Słonecznie (Czyste niebo)", icon: '<i class="fa-solid fa-sun text-amber-500"></i>' },
        1: { desc: "Głównie bezchmurnie", icon: '<i class="fa-solid fa-cloud-sun text-amber-400"></i>' },
        2: { desc: "Częściowe zachmurzenie", icon: '<i class="fa-solid fa-cloud-sun text-gray-400"></i>' },
        3: { desc: "Całkowite zachmurzenie", icon: '<i class="fa-solid fa-cloud text-gray-500"></i>' },
        45: { desc: "Mgła", icon: '<i class="fa-solid fa-smog text-gray-300"></i>' },
        48: { desc: "Oszronienie", icon: '<i class="fa-solid fa-snowflake text-blue-200"></i>' },
        51: { desc: "Lekka mżawka", icon: '<i class="fa-solid fa-cloud-rain text-blue-400"></i>' },
        53: { desc: "Umiarkowana mżawka", icon: '<i class="fa-solid fa-cloud-rain text-blue-500"></i>' },
        55: { desc: "Intensywna mżawka", icon: '<i class="fa-solid fa-cloud-showers-heavy text-blue-600"></i>' },
        61: { desc: "Lekki deszcz", icon: '<i class="fa-solid fa-cloud-rain text-blue-400"></i>' },
        63: { desc: "Umiarkowany deszcz", icon: '<i class="fa-solid fa-cloud-showers-heavy text-blue-500"></i>' },
        65: { desc: "Ulewny deszcz", icon: '<i class="fa-solid fa-cloud-showers-water text-blue-600"></i>' },
        71: { desc: "Lekki opad śniegu", icon: '<i class="fa-solid fa-snowflake text-blue-100"></i>' },
        73: { desc: "Umiarkowany śnieg", icon: '<i class="fa-solid fa-snowflake text-blue-200"></i>' },
        75: { desc: "Silna śnieżyca", icon: '<i class="fa-solid fa-snowflake text-blue-300"></i>' },
        80: { desc: "Lekki przelotny deszcz", icon: '<i class="fa-solid fa-cloud-rain text-blue-400"></i>' },
        81: { desc: "Umiarkowane ulewy", icon: '<i class="fa-solid fa-cloud-showers-heavy text-blue-500"></i>' },
        82: { desc: "Gwałtowne ulewy", icon: '<i class="fa-solid fa-cloud-showers-water text-blue-700"></i>' },
        95: { desc: "Burza", icon: '<i class="fa-solid fa-cloud-bolt text-amber-500 animate-pulse"></i>' }
    };

    function getWeatherInfo(code) {
        return weatherCodes[code] || { desc: "Umiarkowane zachmurzenie", icon: '<i class="fa-solid fa-cloud text-gray-400"></i>' };
    }

    async function loadHomeBiteForecast() {
        const timeEl = document.getElementById('home-live-time');
        const badgeEl = document.getElementById('home-bite-score');
        const labelEl = document.getElementById('home-bite-label');
        
        const ptsPressureEl = document.getElementById('home-pts-pressure');
        const ptsMoonEl = document.getElementById('home-pts-moon');
        const ptsWeatherEl = document.getElementById('home-pts-weather');
        
        const barPressureEl = document.getElementById('home-bar-pressure');
        const barMoonEl = document.getElementById('home-bar-moon');
        const barWeatherEl = document.getElementById('home-bar-weather');
        
        const moonNameEl = document.getElementById('home-moon-name');
        const moonDescEl = document.getElementById('home-moon-desc');
        const moonIconEl = document.getElementById('home-moon-icon');
        const moonRatingEl = document.getElementById('home-moon-rating');
        
        const pressureValEl = document.getElementById('home-pressure-val');
        const pressureTrendEl = document.getElementById('home-pressure-trend');
        const pressureRatingEl = document.getElementById('home-pressure-rating');
        
        const weatherIconEl = document.getElementById('home-weather-icon');
        const weatherTempEl = document.getElementById('home-weather-temp');
        const weatherNameEl = document.getElementById('home-weather-name');
        const weatherDescEl = document.getElementById('home-weather-desc');
        const weatherRatingEl = document.getElementById('home-weather-rating');
        
        const chartTempInfoEl = document.getElementById('home-chart-temp-info');
        const chartPressInfoEl = document.getElementById('home-chart-press-info');
        const goldenHoursEl = document.getElementById('home-golden-hours');
        const locNameEl = document.getElementById('home-loc-name');

        if (!timeEl) return;

        // Dynamic date/time
        const now = new Date();
        const days = ["Niedziela", "Poniedziałek", "Wtorek", "Środa", "Czwartek", "Piątek", "Sobota"];
        const months = ["stycznia", "lutego", "marca", "kwietnia", "maja", "czerwca", "lipca", "sierpnia", "września", "października", "listopada", "grudnia"];
        timeEl.innerHTML = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} o ${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`;

        locNameEl.innerText = locationSource === 'default' ? "Warszawa (Domyślna)" : "Lokalizacja GPS";

        try {
            const url = `https://api.open-meteo.com/v1/forecast?latitude=${activeLat}&longitude=${activeLng}&current=temperature_2m,weather_code,pressure_msl,wind_speed_10m&hourly=temperature_2m,weather_code,pressure_msl&timezone=auto`;
            const res = await fetch(url);
            const data = await res.json();

            if (!data.current) throw new Error("API error");

            const current = data.current;
            const temp = current.temperature_2m;
            const pressure = current.pressure_msl;
            const weatherCode = current.weather_code;
            const windSpeed = current.wind_speed_10m;
            
            // Pressure trend delta
            let pressureTrendValue = 0;
            if (data.hourly && data.hourly.pressure_msl) {
                const hourIndex = now.getHours();
                const pastPressure = data.hourly.pressure_msl[Math.max(0, hourIndex - 3)];
                pressureTrendValue = pressure - pastPressure;
            }

            // Calculations
            const moon = getMoonPhase(now);
            
            // Sub-scores
            let scorePressurePts = 5;
            if (pressure >= 1010 && pressure <= 1020) scorePressurePts = 9;
            else if (pressure < 995 || pressure > 1030) scorePressurePts = 2;
            else scorePressurePts = 6;
            if (pressureTrendValue < 0) scorePressurePts = Math.min(10, scorePressurePts + 1);

            let scoreMoonPts = Math.round((moon.score / 100) * 10);
            
            let scoreWeatherPts = 5;
            if ([0, 1, 2, 51, 61].includes(weatherCode)) scoreWeatherPts = 8;
            else if ([80, 81, 82, 95].includes(weatherCode)) scoreWeatherPts = 2;

            // Update subscore text & bars
            ptsPressureEl.innerText = `${scorePressurePts}/10 pkt`;
            ptsMoonEl.innerText = `${scoreMoonPts}/10 pkt`;
            ptsWeatherEl.innerText = `${scoreWeatherPts}/10 pkt`;
            
            barPressureEl.style.width = `${scorePressurePts * 10}%`;
            barMoonEl.style.width = `${scoreMoonPts * 10}%`;
            barWeatherEl.style.width = `${scoreWeatherPts * 10}%`;

            // Moon details
            moonNameEl.innerText = moon.name;
            moonDescEl.innerText = moon.desc;
            moonIconEl.innerHTML = moon.icon;
            moonRatingEl.innerText = moonScoreLabel(moon.score);

            // Pressure details
            pressureValEl.innerText = Math.round(pressure);
            if (pressureTrendValue > 0.4) {
                pressureTrendEl.innerHTML = `<i class="fa-solid fa-arrow-trend-up"></i> +${pressureTrendValue.toFixed(1)} hPa/3h (Wzrost)`;
                pressureTrendEl.className = "text-[11px] font-bold text-red-500 mt-1";
                pressureRatingEl.innerText = "Niekorzystne szybkie ciśnienie";
                pressureRatingEl.className = "text-xs font-black uppercase tracking-widest text-red-500";
            } else if (pressureTrendValue < -0.4) {
                pressureTrendEl.innerHTML = `<i class="fa-solid fa-arrow-trend-down"></i> ${pressureTrendValue.toFixed(1)} hPa/3h (Spadek)`;
                pressureTrendEl.className = "text-[11px] font-bold text-emerald-500 mt-1";
                pressureRatingEl.innerText = "Spadek stymuluje brania!";
                pressureRatingEl.className = "text-xs font-black uppercase tracking-widest text-emerald-500";
            } else {
                pressureTrendEl.innerHTML = `<i class="fa-solid fa-equals"></i> ${pressureTrendValue >= 0 ? '+' : ''}${pressureTrendValue.toFixed(1)} hPa/3h (Stabilne)`;
                pressureTrendEl.className = "text-[11px] font-bold text-blue-500 mt-1";
                pressureRatingEl.innerText = "Stabilne optymalne warunki";
                pressureRatingEl.className = "text-xs font-black uppercase tracking-widest text-blue-600";
            }

            // Weather details
            const wInfo = getWeatherInfo(weatherCode);
            weatherIconEl.innerHTML = wInfo.icon;
            weatherTempEl.innerText = `${temp.toFixed(1)}°C`;
            weatherNameEl.innerText = wInfo.desc;
            weatherDescEl.innerText = `Wiatr: ${windSpeed.toFixed(1)} m/s`;
            
            // Ratings
            weatherRatingEl.innerText = weatherCodeRatingLabel(weatherCode);

            // Overall Bite Score
            const currentHour = now.getHours();
            const score = calculateDynamicBite(temp, pressure, pressureTrendValue, moon.score, weatherCode, currentHour);

            badgeEl.innerText = `${score}/100`;
            badgeEl.className = "text-white font-black px-4 py-2 rounded-2xl shadow-md text-base transition-all duration-500 min-w-[65px] text-center";
            if (score >= 75) {
                badgeEl.classList.add('bg-emerald-600', 'border', 'border-emerald-500');
                labelEl.innerText = "Doskonałe!";
                labelEl.className = "text-emerald-600 text-xs font-black uppercase tracking-widest";
            } else if (score >= 50) {
                badgeEl.classList.add('bg-amber-500', 'border', 'border-amber-400');
                labelEl.innerText = "Średnie";
                labelEl.className = "text-amber-500 text-xs font-black uppercase tracking-widest";
            } else {
                badgeEl.classList.add('bg-red-500', 'border', 'border-red-400');
                labelEl.innerText = "Słabe";
                labelEl.className = "text-red-500 text-xs font-black uppercase tracking-widest";
            }

            // Chart details
            chartTempInfoEl.innerHTML = `${temp.toFixed(1)}°C ${wInfo.icon}`;
            chartPressInfoEl.innerHTML = `<i class="fa-solid fa-gauge text-blue-400"></i> ${Math.round(pressure)} hPa`;

            // Draw SVG Spline for 24h (zakres całej doby 0:00 - 23:59)
            if (data.hourly) {
                let hourlyScores = [];
                // Pełne 24 godziny
                const forecastHours = Array.from({ length: 24 }, (_, i) => i);
                
                forecastHours.forEach(h => {
                    const hTemp = data.hourly.temperature_2m[h] !== undefined ? data.hourly.temperature_2m[h] : temp;
                    const hPressure = data.hourly.pressure_msl[h] !== undefined ? data.hourly.pressure_msl[h] : pressure;
                    const hCode = data.hourly.weather_code[h] !== undefined ? data.hourly.weather_code[h] : weatherCode;
                    const hScore = calculateDynamicBite(hTemp, hPressure, pressureTrendValue, moon.score, hCode, h);
                    hourlyScores.push({ hour: h, score: hScore });
                });

                // Wyznaczenie progu aktywności na podstawie amplitudy
                const scores = hourlyScores.map(s => s.score);
                const maxScore = Math.max(...scores);
                const minScore = Math.min(...scores);
                const delta = maxScore - minScore;

                // Obliczenie progu szczytowego (górne 35% różnicy)
                let threshold = maxScore - 3;
                if (delta > 4) {
                    threshold = minScore + delta * 0.65;
                } else {
                    threshold = 999;
                }

                // Wyznaczenie przedziałów o podwyższonej aktywności
                let goodBiteIntervals = [];
                let currentInterval = null;
                
                hourlyScores.forEach(s => {
                    if (s.score >= threshold) {
                        if (!currentInterval) {
                            currentInterval = { start: s.hour, end: s.hour };
                        } else {
                            currentInterval.end = s.hour;
                        }
                    } else {
                        if (currentInterval) {
                            goodBiteIntervals.push(currentInterval);
                            currentInterval = null;
                        }
                    }
                });
                if (currentInterval) {
                    goodBiteIntervals.push(currentInterval);
                }

                // Generowanie opisu tekstowego najlepszych godzin
                let intervalsText = goodBiteIntervals.map(i => {
                    if (i.start === i.end) return `${i.start}:00`;
                    return `${i.start}:00 - ${i.end}:00`;
                }).join(", ");

                goldenHoursEl.innerText = intervalsText.length > 0 ? intervalsText : "Brak wyraźnych przedziałów o wysokiej aktywności dzisiaj";

                const width = 100;
                const height = 30;
                let points = [];
                hourlyScores.forEach((s, idx) => {
                    const x = (idx / (hourlyScores.length - 1)) * width;
                    const y = height - (s.score / 100) * (height - 5);
                    points.push({ x, y, hour: s.hour });
                });

                let pathD = `M 0,${height} `;
                points.forEach((p, idx) => {
                    if (idx === 0) pathD += `L ${p.x.toFixed(1)},${p.y.toFixed(1)} `;
                    else {
                        const prev = points[idx - 1];
                        // Obliczanie punktów kontrolnych dla krzywej Beziera trzeciego stopnia (Cubic Bezier).
                        // cpX1 i cpX2 są umieszczone dokładnie w połowie odległości poziomej między punktami (p.x - prev.x) / 2.
                        // Ustawienie wysokości punktów kontrolnych na wartości równe y punktu początkowego (cpY1 = prev.y)
                        // oraz punktu docelowego (cpY2 = p.y) zapewnia płynne, łagodne zaokrąglenie linii wykresu (tangent poziom),
                        // eliminując nienaturalne załamania lub ostre skoki wysokości linii.
                        const cpX1 = prev.x + (p.x - prev.x) / 2;
                        const cpY1 = prev.y;
                        const cpX2 = prev.x + (p.x - prev.x) / 2;
                        const cpY2 = p.y;
                        pathD += `C ${cpX1.toFixed(1)},${cpY1.toFixed(1)} ${cpX2.toFixed(1)},${cpY2.toFixed(1)} ${p.x.toFixed(1)},${p.y.toFixed(1)} `;
                    }
                });
                
                const fillD = pathD + `L ${width},${height} L 0,${height} Z`;
                
                document.getElementById('home-chart-line').setAttribute('d', pathD);
                document.getElementById('home-chart-fill').setAttribute('d', fillD);

                // Czyszczenie wyróżnionych przedziałów na linii SVG (usunięcie podświetleń)
                const highlightsContainer = document.getElementById('home-chart-highlights');
                if (highlightsContainer) {
                    highlightsContainer.innerHTML = '';
                }

                // Generowanie wskaźnika pionowego i punktu aktualnego czasu (nakładka HTML)
                const overlay = document.getElementById('home-chart-overlay');
                if (overlay) {
                    overlay.innerHTML = '';

                    const currentHourIdx = forecastHours.findIndex(h => h >= currentHour);
                    if (currentHourIdx !== -1) {
                        const targetPoint = points[currentHourIdx];
                        const yPercent = (targetPoint.y / height) * 100;

                        // Linia pionowa
                        const vLine = document.createElement('div');
                        vLine.className = "absolute top-0 bottom-0 border-l border-dashed border-amber-500/80 transform -translate-x-1/2";
                        vLine.style.left = `${targetPoint.x}%`;
                        overlay.appendChild(vLine);

                        // Pulsujący marker
                        const currentDot = document.createElement('div');
                        currentDot.className = "absolute w-2.5 h-2.5 bg-amber-500 border border-emerald-950 rounded-full transform -translate-x-1/2 -translate-y-1/2 animate-pulse shadow-md shadow-amber-500/80";
                        currentDot.style.left = `${targetPoint.x}%`;
                        currentDot.style.top = `${yPercent}%`;
                        overlay.appendChild(currentDot);
                    }
                }
            }

        } catch (e) {
            console.error(e);
        }
    }

    function moonScoreLabel(score) {
        if (score >= 80) return "Bardzo mocny - wybitny";
        if (score >= 60) return "Umiarkowanie korzystny";
        if (score >= 45) return "Przeciętna faza";
        return "Słaba aktywność w nocy";
    }

    function weatherCodeRatingLabel(code) {
        if ([0, 1].includes(code)) return "Słonecznie - dobre";
        if ([2, 3].includes(code)) return "Zachmurzenie - stabilne";
        if ([51, 61, 80].includes(code)) return "Mżawka - idealna!";
        return "Gwałtowne opady - słabe";
    }

    // Algorytm obliczania wskaźnika brań (Bite Index) w skali od 0 do 100.
    // Sumuje i waży czynniki meteorologiczne oraz astronomiczne wpływające na żerowanie ryb słodkowodnych:
    // 1. Temperatura wody/powietrza (optimum termiczne: 12-19°C)
    // 2. Ciśnienie atmosferyczne (stabilność barometryczna: 1010-1020 hPa)
    // 3. Trend ciśnienia (powolny spadek pobudza ryby, gwałtowne wahania (>0.4 hPa/3h) hamują żerowanie)
    // 4. Faza księżyca (nów i pełnia silnie stymulują aktywność nocną i dobową)
    // 5. Zachmurzenie/Opady (słaba mżawka natlenia wodę i zmniejsza widoczność, ułatwiając brania drapieżników)
    // 6. Pora doby (szczyty aktywności - żerowanie świtowe i zmierzchowe)
    function calculateDynamicBite(temp, pressure, pressureTrend, moonScore, weatherCode, hour) {
        let score = 50; // Punkt wyjściowy
        
        // Czynnik 1: Temperatura powietrza/wody
        if (temp >= 12 && temp <= 19) score += 15;
        else if (temp < 6 || temp > 28) score -= 15; // Ekstrema temperaturowe wprowadzają ryby w letarg
        else score += 5;

        // Czynnik 2: Ciśnienie atmosferyczne
        if (pressure >= 1010 && pressure <= 1020) score += 15;
        else if (pressure < 995 || pressure > 1030) score -= 20; // Anomalia ciśnieniowe
        else score += 5;

        // Czynnik 3: Trend ciśnienia barometrycznego
        if (Math.abs(pressureTrend) < 0.4) score += 10;          // Stabilne ciśnienie
        else if (pressureTrend < 0) score += 15;                 // Spadek ciśnienia (zazwyczaj zwiastuje zachmurzenie/deszcz)
        else score -= 10;                                        // Gwałtowny wzrost (często chłodny front)

        // Czynnik 4: Faza księżyca (wpływ grawitacyjny i oświetleniowy)
        score += (moonScore - 50) * 0.3;

        // Czynnik 5: Typ pogody (kod WMO Open-Meteo)
        if ([1, 2, 51, 61].includes(weatherCode)) score += 12;   // Lekkie chmury, mżawka
        else if ([80, 81, 82, 95].includes(weatherCode)) score -= 22; // Ulewa, burza (ryby kryją się przy dnie)

        // Czynnik 6: Dobowa aktywność żerowania (Golden Hours)
        if ((hour >= 5 && hour <= 8) || (hour >= 18 && hour <= 21)) score += 20; // Świt i zmierzch
        else if (hour >= 11 && hour <= 15) score -= 10;                         // Środek dnia (ryby słabiej reagują)

        // Ograniczenie wyniku w bezpiecznych granicach (min 8%, max 96%)
        return Math.max(8, Math.min(96, Math.round(score)));
    }

    window.toggleHomeLocationSource = function() {
        const btn = document.querySelector('button[onclick="toggleHomeLocationSource()"]');
        if (locationSource === 'default') {
            if (navigator.geolocation) {
                btn.innerText = "Czekaj...";
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        activeLat = position.coords.latitude;
                        activeLng = position.coords.longitude;
                        locationSource = 'gps';
                        btn.innerText = "Zmień na Domyślne";
                        document.getElementById('home-loc-icon').className = "fa-solid fa-street-view animate-bounce text-emerald-700 text-sm";
                        loadHomeBiteForecast();
                    },
                    (error) => {
                        alert("Błąd geolokalizacji. Upewnij się, że zezwoliłeś na pobranie lokalizacji.");
                        btn.innerText = "Zmień na GPS";
                    }
                );
            } else {
                alert("Geolokalizacja nie jest obsługiwana.");
            }
        } else {
            activeLat = defaultLat;
            activeLng = defaultLng;
            locationSource = 'default';
            btn.innerText = "Zmień na GPS";
            document.getElementById('home-loc-icon').className = "fa-solid fa-location-crosshairs text-emerald-700 text-sm animate-pulse";
            loadHomeBiteForecast();
        }
    };

    // Load on startup
    window.addEventListener('load', () => {
        loadHomeBiteForecast();
        setInterval(loadHomeBiteForecast, 60000);
    });

    // Globalne funkcje modala
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

    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    }
</script>

</body>
</html>
