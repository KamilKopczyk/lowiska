<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Mapa Łowisk - Wędkarski Radar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2">
    <link rel="apple-touch-icon" href="/favicon.svg?v=2">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        .no-select { user-select: none; -webkit-user-select: none; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 8px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }

        /* Nadpisanie stylów Leaflet, żeby dymki i linki wyglądały nowocześnie */
        .leaflet-popup-content-wrapper { border-radius: 0.75rem; overflow: hidden; padding: 0; }
        .leaflet-popup-content { margin: 0; width: 220px !important; }
        .leaflet-container a.leaflet-popup-close-button { color: white; text-shadow: 0 1px 2px black; font-size: 18px; top: 4px; right: 4px; z-index: 10; }
        .btn-szczegoly { color: white !important; text-decoration: none !important; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-800 flex flex-col min-h-screen h-screen overflow-hidden">

    <nav id="main-navbar" class="bg-emerald-900/95 backdrop-blur-md text-white shadow-md relative z-40 shrink-0">
        <div id="main-navbar-container" class="container mx-auto flex flex-wrap justify-between items-center px-4 py-4">
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
                <a href="/lowiska" class="hover:text-amber-400 transition flex items-center w-fit">
                    <i class="fa-solid fa-water mr-2"></i> Wszystkie łowiska
                </a>
                <a href="/mapa" class="text-amber-400 flex items-center border-b-2 border-amber-400 pb-1 w-fit">
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

    <main class="flex-grow flex flex-col md:flex-row relative h-full">

        <aside class="absolute md:relative top-0 left-0 w-full md:w-[350px] bg-white/95 md:bg-white backdrop-blur-md shadow-[0_4px_20px_rgba(0,0,0,0.15)] md:shadow-[4px_0_10px_rgba(0,0,0,0.05)] border-b md:border-r border-gray-200 z-20 flex flex-col shrink-0 max-h-[40vh] md:max-h-none md:h-full overflow-y-auto custom-scrollbar">
            <div class="p-5">
                <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Filtruj na mapie</h2>

                <form action="/mapa" method="GET" class="space-y-6">

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-3">Miejscowość i zasięg</label>
                        <div class="space-y-4">

                            <div class="relative">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
                                <input type="text" name="miejscowosc" id="miejscowosc-input" value="{{ request('miejscowosc') }}" placeholder="np. Krosno..." autocomplete="off" class="w-full pl-10 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">

                                <ul id="miejscowosc-suggestions" class="hidden absolute z-50 w-full bg-white border border-gray-200 rounded-lg mt-1 shadow-xl max-h-60 overflow-y-auto custom-scrollbar top-full left-0 divide-y divide-gray-50">
                                    </ul>
                            </div>

                            <div class="px-1">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-semibold text-gray-500">Promień szukania:</span>
                                    <span class="text-sm font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100" id="zasieg-wartosc">{{ request('zasieg', 50) }} km</span>
                                </div>
                                <input type="range" name="zasieg" id="zasieg-slider" min="0" max="500" step="5" value="{{ request('zasieg', 50) }}" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-emerald-600">
                                <div class="flex justify-between text-[10px] text-gray-400 mt-1 font-medium">
                                    <span>0 km</span>
                                    <span>Max 500 km</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Typ łowiska</label>
                        <select name="typ" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                            <option value="Wszystkie" {{ request('typ') == 'Wszystkie' ? 'selected' : '' }}>Wszystkie</option>
                            <option value="jezioro" {{ request('typ') == 'jezioro' ? 'selected' : '' }}>Jezioro</option>
                            <option value="rzeka" {{ request('typ') == 'rzeka' ? 'selected' : '' }}>Rzeka</option>
                            <option value="zalew" {{ request('typ') == 'zalew' ? 'selected' : '' }}>Zalew</option>
                        </select>
                    </div>

                    <div class="relative" id="custom-gatunek-select">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Gatunek ryby</label>
                        <input type="hidden" name="gatunek" id="gatunek-input" value="{{ request('gatunek', 'Wszystkie') }}">
                        <button type="button" id="gatunek-button" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 flex justify-between items-center transition">
                            <span id="gatunek-display" class="truncate font-medium text-gray-700">{{ request('gatunek', 'Wszystkie') }}</span>
                            <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-200" id="gatunek-arrow"></i>
                        </button>
                        <ul id="gatunek-dropdown" class="hidden absolute z-50 w-full bg-white border border-gray-200 rounded-lg mt-1 shadow-lg max-h-56 overflow-y-auto custom-scrollbar top-full left-0">
                            <li class="px-3 py-2 hover:bg-emerald-50 hover:text-emerald-700 cursor-pointer text-sm font-medium" data-value="Wszystkie">Wszystkie</li>
                            @if(isset($allFishes))
                                @foreach($allFishes as $fish)
                                    <li class="px-3 py-2 hover:bg-emerald-50 hover:text-emerald-700 cursor-pointer text-sm" data-value="{{ $fish->name }}">{{ $fish->name }}</li>
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
                                <span id="ocena-display" class="ml-3 text-sm font-bold text-emerald-700 bg-emerald-50 px-2 py-1 rounded-md border border-emerald-200 min-w-[3rem] text-center">
                                    {{ request('ocena') != 'Wszystkie' && request('ocena') ? request('ocena') . '+' : 'Brak' }}
                                </span>
                            </div>
                            <button type="button" id="clear-ocena-btn" class="text-xs text-red-500 hover:text-red-700 mt-2 self-start transition font-medium {{ request('ocena') != 'Wszystkie' && request('ocena') ? '' : 'hidden' }}">
                                <i class="fa-solid fa-xmark mr-1"></i> Wyczyść ocenę
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-2.5 rounded-lg transition shadow-md mt-2">
                        Pokaż na mapie
                    </button>

                    @if(request()->has('miejscowosc') || request()->has('typ') || request()->has('gatunek') || request()->has('ocena'))
                        <a href="/mapa" class="block text-center text-sm text-gray-500 hover:text-red-500 mt-2 transition">Wyczyść wszystkie filtry</a>
                    @endif
                </form>
            </div>
        </aside>

        <section class="flex-grow relative z-0 h-full">
            <div id="map" class="w-full h-full bg-gray-200"></div>

            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-4 py-2 rounded-lg shadow-lg border border-gray-200 z-[1000] font-bold text-gray-800 flex items-center">
                <i class="fa-solid fa-map-pin text-emerald-600 mr-2 text-lg"></i> Znaleziono: <span id="wyniki-count" class="ml-1">Ładowanie...</span>
            </div>
        </section>

    </main>

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

            // Inicjalizacja mapy
            var map = L.map('map').setView([52.069, 19.480], 6);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            var fishingSpots = @json($spots);
            var searchedCity = "{{ request('miejscowosc') }}";
            var searchRadius = "{{ request('zasieg') }}";

            var visibleMarkersCount = 0;
            var markersLayerGroup = L.layerGroup().addTo(map);

            // Wygląd ikon na mapie
            var fishIcon = L.divIcon({
                className: 'custom-fish-icon',
                html: `<div style="background-color: #047857; width: 32px; height: 32px; border-radius: 50%; border: 2px solid white; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.3); font-size: 14px;">
                        <i class="fa-solid fa-fish"></i>
                       </div>`,
                iconSize: [32, 32],
                iconAnchor: [16, 16],
                popupAnchor: [0, -16]
            });

            var originIcon = L.divIcon({
                className: 'custom-origin-icon',
                html: `<div style="background-color: #ef4444; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 15px rgba(239, 68, 68, 0.8);">
                        <div style="width: 8px; height: 8px; background-color: white; border-radius: 50%;"></div>
                       </div>`,
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });

            // Renderowanie pojedynczego markera
            function createMarker(spot, lat, lng) {
                var avgRating = spot.reviews_avg_rating ? parseFloat(spot.reviews_avg_rating).toFixed(1) : '0.0';
                var slug = spot.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
                var spotUrl = "/lowiska/" + spot.id + "-" + slug;

                var popupContent = `
                    <div class="relative bg-white pb-2 flex flex-col">
                        <img src="${spot.main_image_url}" class="w-full h-28 object-cover bg-gray-100">
                        <div class="absolute top-2 left-2 bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow uppercase">${spot.type}</div>
                        <div class="px-3 pt-3">
                            <h3 class="font-bold text-gray-900 text-base leading-tight mb-1">${spot.name}</h3>
                            <p class="text-xs text-emerald-700 font-medium mb-2"><i class="fa-solid fa-location-dot"></i> ${spot.city}</p>
                            <div class="flex items-center justify-between mb-3 border-t border-gray-100 pt-2">
                                <span class="text-xs text-gray-500"><i class="fa-regular fa-comments mr-1"></i> ${spot.reviews ? spot.reviews.length : 0}</span>
                                <div class="flex items-center ${spot.reviews && spot.reviews.length > 0 ? 'bg-amber-50 border-amber-100' : 'bg-gray-50 border-gray-100'} px-1.5 py-0.5 rounded border">
                                    <i class="fa-solid fa-star ${spot.reviews && spot.reviews.length > 0 ? 'text-amber-500' : 'text-gray-300'} text-[10px] mr-1"></i>
                                    <span class="font-bold text-gray-800 text-xs">${avgRating}</span>
                                </div>
                            </div>
                            <a href="${spotUrl}" class="btn-szczegoly block w-full bg-emerald-700 hover:bg-emerald-800 text-center text-sm font-bold py-2 rounded transition shadow-sm">
                                Szczegóły <i class="fa-solid fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                `;

                var marker = L.marker([lat, lng], {icon: fishIcon});
                marker.bindPopup(popupContent);
                markersLayerGroup.addLayer(marker);
                visibleMarkersCount++;
            }

            // Renderowanie wszystkich markerów
            function drawAllMarkers() {
                visibleMarkersCount = 0;
                markersLayerGroup.clearLayers();

                fishingSpots.forEach(function(spot) {
                    var pseudoRandomLat = 49.5 + ((spot.id * 17.3) % 4.5);
                    var pseudoRandomLng = 15.0 + ((spot.id * 23.1) % 8.0);
                    var lat = spot.lat ? parseFloat(spot.lat) : pseudoRandomLat;
                    var lng = spot.lng ? parseFloat(spot.lng) : pseudoRandomLng;
                    createMarker(spot, lat, lng);
                });

                document.getElementById('wyniki-count').innerText = visibleMarkersCount;
            }

            // Wyszukiwanie w promieniu od danej miejscowości
            if (searchedCity && searchRadius && parseInt(searchRadius) > 0) {
                fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(searchedCity + ', Polska'))
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            var centerLat = parseFloat(data[0].lat);
                            var centerLng = parseFloat(data[0].lon);
                            var radiusMeters = parseInt(searchRadius) * 1000;

                            var circle = L.circle([centerLat, centerLng], {
                                color: '#10b981', weight: 2, fillColor: '#10b981', fillOpacity: 0.15, radius: radiusMeters
                            }).addTo(map);

                            L.marker([centerLat, centerLng], {icon: originIcon})
                             .addTo(map)
                             .bindPopup(`<div class="text-center font-bold text-gray-800 p-1">Szukasz wokół:<br><span class="text-red-500">${searchedCity}</span></div>`);

                            map.fitBounds(circle.getBounds());

                            var centerLatLng = L.latLng(centerLat, centerLng);
                            visibleMarkersCount = 0;
                            markersLayerGroup.clearLayers();

                            fishingSpots.forEach(function(spot) {
                                var pseudoRandomLat = 49.5 + ((spot.id * 17.3) % 4.5);
                                var pseudoRandomLng = 15.0 + ((spot.id * 23.1) % 8.0);
                                var lat = spot.lat ? parseFloat(spot.lat) : pseudoRandomLat;
                                var lng = spot.lng ? parseFloat(spot.lng) : pseudoRandomLng;

                                var spotLatLng = L.latLng(lat, lng);

                                if (centerLatLng.distanceTo(spotLatLng) <= radiusMeters) {
                                    createMarker(spot, lat, lng);
                                }
                            });

                            document.getElementById('wyniki-count').innerText = visibleMarkersCount;

                        } else {
                            alert("Nie udało się znaleźć lokalizacji na mapie. Wybierz miejscowość z listy podpowiedzi.");
                            drawAllMarkers();
                        }
                    })
                    .catch(error => {
                        console.error("Błąd API mapy:", error); drawAllMarkers();
                    });

            } else {
                drawAllMarkers();
            }

            // Obsługa autouzupełniania miejscowości
            const miastoInput = document.getElementById('miejscowosc-input');
            const miastoSuggestions = document.getElementById('miejscowosc-suggestions');
            let debounceTimer;

            if (miastoInput && miastoSuggestions) {
                miastoInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    const query = this.value.trim();

                    if (query.length < 3) {
                        miastoSuggestions.classList.add('hidden');
                        return;
                    }

                    debounceTimer = setTimeout(() => {
                        miastoSuggestions.innerHTML = '<li class="px-4 py-3 text-sm text-gray-500"><i class="fa-solid fa-spinner fa-spin mr-2 text-emerald-600"></i> Szukam...</li>';
                        miastoSuggestions.classList.remove('hidden');

                        // Wyszukiwanie miejscowości przez Nominatim API
                        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=pl&addressdetails=1&limit=30&featuretype=settlement`)
                            .then(res => res.json())
                            .then(data => {
                                miastoSuggestions.innerHTML = '';
                                if (data.length === 0) {
                                    miastoSuggestions.innerHTML = '<li class="px-4 py-3 text-sm text-gray-500">Nie znaleziono miejscowości</li>';
                                    return;
                                }

                                const uniquePlaces = new Set();

                                data.forEach(item => {
                                    let placeName = item.name;

                                    if (placeName) {
                                        placeName = placeName.replace(/^gmina\s+/i, '').replace(/^miasto\s+/i, '');
                                    }

                                    const state = item.address && item.address.state ? item.address.state.replace('województwo ', '') : '';
                                    const county = item.address && item.address.county ? item.address.county : '';
                                    const municipality = item.address && item.address.municipality ? item.address.municipality : '';
                                    
                                    let detail = '';
                                    if (county) {
                                        detail = county;
                                    } else if (municipality) {
                                        detail = municipality;
                                    }

                                    // Filtrowanie i unikanie duplikatów
                                    if (placeName && state && placeName.toLowerCase().includes(query.toLowerCase())) {
                                        const displayName = detail ? `${placeName} (${detail}), ${state}` : `${placeName}, ${state}`;

                                        if (!uniquePlaces.has(displayName)) {
                                            uniquePlaces.add(displayName);

                                            const li = document.createElement('li');
                                            li.className = 'px-4 py-2.5 hover:bg-emerald-50 cursor-pointer flex justify-between items-center transition group';
                                            li.innerHTML = `
                                                <div class="flex flex-col text-left">
                                                    <span class="font-bold text-gray-800 group-hover:text-emerald-700">${placeName}</span>
                                                    ${detail ? `<span class="text-[11px] text-gray-400 font-medium">${detail}</span>` : ''}
                                                </div>
                                                <span class="text-[10px] font-bold text-gray-500 uppercase bg-gray-100 px-2 py-1 rounded-md border border-gray-200 group-hover:bg-emerald-100 group-hover:text-emerald-700 group-hover:border-emerald-200 transition">${state}</span>
                                            `;

                                            li.addEventListener('click', () => {
                                                miastoInput.value = displayName;
                                                miastoSuggestions.classList.add('hidden');
                                            });
                                            miastoSuggestions.appendChild(li);
                                        }
                                    }
                                });

                                if(uniquePlaces.size === 0) {
                                     miastoSuggestions.innerHTML = '<li class="px-4 py-3 text-sm text-gray-500">Brak dokładnego dopasowania</li>';
                                }
                             })
                            .catch(err => {
                                miastoSuggestions.innerHTML = '<li class="px-4 py-3 text-sm text-red-500">Błąd serwera mapy</li>';
                            });
                    }, 400);
                });

                document.addEventListener('click', (e) => {
                    if (!miastoInput.contains(e.target) && !miastoSuggestions.contains(e.target)) {
                        miastoSuggestions.classList.add('hidden');
                    }
                });
            }

            // Obsługa suwaka zasięgu
            const zasiegSlider = document.getElementById('zasieg-slider');
            const zasiegWartosc = document.getElementById('zasieg-wartosc');
            if (zasiegSlider && zasiegWartosc) {
                zasiegSlider.addEventListener('input', function() { zasiegWartosc.textContent = this.value + ' km'; });
            }

            // Obsługa ocen (gwiazdki)
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
                    if (value >= starIndex) { icon.classList.add('fa-solid', 'text-amber-500'); }
                    else if (value === starIndex - 0.5) { icon.classList.add('fa-solid', 'fa-star-half-stroke', 'text-amber-500'); }
                    else { icon.classList.add('fa-regular', 'text-gray-300'); }
                });
            }
            renderStars(currentRatingValue);

            stars.forEach(star => {
                star.addEventListener('mousemove', function(e) {
                    const isHalf = (e.clientX - this.getBoundingClientRect().left) < this.getBoundingClientRect().width / 2;
                    renderStars(isHalf ? parseFloat(this.getAttribute('data-index')) - 0.5 : parseFloat(this.getAttribute('data-index')));
                });
                star.addEventListener('click', function(e) {
                    const isHalf = (e.clientX - this.getBoundingClientRect().left) < this.getBoundingClientRect().width / 2;
                    currentRatingValue = isHalf ? parseFloat(this.getAttribute('data-index')) - 0.5 : parseFloat(this.getAttribute('data-index'));
                    hiddenOcenaInput.value = currentRatingValue;
                    ocenaDisplay.textContent = currentRatingValue + '+';
                    clearOcenaBtn.classList.remove('hidden');
                    renderStars(currentRatingValue);
                });
            });
            starContainer.addEventListener('mouseleave', () => renderStars(currentRatingValue));
            clearOcenaBtn.addEventListener('click', () => {
                currentRatingValue = 0; hiddenOcenaInput.value = 'Wszystkie'; ocenaDisplay.textContent = 'Brak';
                clearOcenaBtn.classList.add('hidden'); renderStars(0);
            });

            // Obsługa dropdownu gatunków
            const gatunekBtn = document.getElementById('gatunek-button');
            const gatunekDropdown = document.getElementById('gatunek-dropdown');
            const gatunekInput = document.getElementById('gatunek-input');
            const gatunekDisplay = document.getElementById('gatunek-display');
            const gatunekArrow = document.getElementById('gatunek-arrow');
            gatunekBtn.addEventListener('click', function(e) {
                e.preventDefault(); gatunekDropdown.classList.toggle('hidden'); gatunekArrow.classList.toggle('rotate-180');
            });
            gatunekDropdown.querySelectorAll('li').forEach(option => {
                option.addEventListener('click', function() {
                    gatunekInput.value = this.getAttribute('data-value'); gatunekDisplay.textContent = this.getAttribute('data-value');
                    gatunekDropdown.classList.add('hidden'); gatunekArrow.classList.remove('rotate-180');
                });
            });
            document.addEventListener('click', function(e) {
                if (!document.getElementById('custom-gatunek-select').contains(e.target)) {
                    gatunekDropdown.classList.add('hidden'); gatunekArrow.classList.remove('rotate-180');
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

    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    }
    </script>


</body>
</html>
