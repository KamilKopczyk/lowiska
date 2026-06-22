<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>{{ $spot->name }} - Wędkarski Radar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2">
    <link rel="apple-touch-icon" href="/favicon.svg?v=2">
    <style>
        .no-select { user-select: none; -webkit-user-select: none; }
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
                <a href="/lowiska" class="text-emerald-100 hover:text-amber-400 transition flex items-center w-fit">
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

    <main class="container mx-auto px-4 py-8 flex-grow max-w-7xl">

        @if(session('success'))
            <div class="mb-5 p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 font-bold rounded-xl flex items-center gap-3 shadow-sm">
                <i class="fa-solid fa-circle-check text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="mb-4 flex justify-between items-center">
            <a href="javascript:history.back()" class="text-gray-500 hover:text-emerald-700 font-medium flex items-center text-sm transition">
                <i class="fa-solid fa-chevron-left mr-2"></i> Powrót
            </a>
            @auth
                @if(Auth::user()->isAdmin())
                    <button onclick="openEditSpotModal()" class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-4 py-2 rounded-lg transition flex items-center shadow-sm text-sm">
                        <i class="fa-solid fa-pen-to-square mr-2"></i> Edytuj łowisko
                    </button>
                @endif
            @endauth
        </div>

        <div class="w-full h-64 md:h-96 relative rounded-2xl overflow-hidden mb-8 shadow-md group">
            <img src="{{ $spot->main_image_url }}" alt="{{ $spot->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>

            <div class="absolute top-4 right-4 bg-white text-gray-800 text-xs font-bold px-4 py-1.5 rounded-lg shadow-lg uppercase tracking-wider">
                {{ $spot->type }}
            </div>

            <div class="absolute bottom-6 left-6 text-white">
                <h1 class="text-4xl md:text-5xl font-extrabold mb-2 drop-shadow-lg">{{ $spot->name }}</h1>
                <p class="text-lg text-emerald-50 drop-shadow-md font-medium"><i class="fa-solid fa-location-dot text-amber-400 mr-2"></i>{{ $spot->city }}, {{ $spot->region }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <div class="md:col-span-2 space-y-8">

                <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Opis</h2>
                    <p class="text-gray-600 leading-relaxed">{{ $spot->description }}</p>
                </section>

                <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Udogodnienia</h2>
                    <div class="flex flex-wrap gap-3">
                        @forelse($spot->amenities as $amenity)
                            <span class="bg-gray-100 text-gray-700 px-3 py-1.5 rounded-lg text-sm font-medium flex items-center border border-gray-200">
                                <i class="fa-solid fa-check text-emerald-600 mr-2"></i> {{ $amenity->name }}
                            </span>
                        @empty
                            <p class="text-sm text-gray-500">Brak informacji o udogodnieniach.</p>
                        @endforelse
                    </div>
                </section>

                <section class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-2">
                        <h2 class="text-xl font-bold text-gray-800">Struktura zarybienia</h2>
                        <i class="fa-solid fa-chart-pie text-gray-400 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-500 mb-6 border-b border-gray-100 pb-4">Szacunkowy procentowy udział poszczególnych gatunków w tym akwenie.</p>

                    <div class="space-y-5">
                        @forelse($spot->fish_species as $fish)
                            @php
                                $percent = $fish->pivot->percentage ?? 0;
                            @endphp
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <span class="font-semibold text-gray-700 text-sm flex items-center">
                                        <i class="fa-solid fa-fish text-emerald-600 mr-2 w-4 text-center"></i> {{ $fish->name }}
                                    </span>
                                    <span class="text-sm font-bold text-gray-500">{{ $percent }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-emerald-500 h-2.5 rounded-full" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-2">Brak informacji o zarybieniu.</p>
                        @endforelse
                    </div>
                </section>

                <section id="rekordy" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-xl font-bold text-gray-800">Ranking Złowionych Ryb ({{ $spot->fish_records->count() }})</h2>
                        <i class="fa-solid fa-trophy text-amber-500"></i>
                    </div>

                    <div class="p-6 border-b border-gray-100">
                        @if($spot->fish_records->count() > 0)
                            <div class="space-y-4">
                                @foreach($spot->fish_records->sortByDesc('weight')->values() as $index => $record)
                                    <div class="flex items-center justify-between p-4 bg-white border border-gray-100 rounded-xl hover:shadow-md transition shadow-sm">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-10 h-10 flex items-center justify-center rounded-full font-bold shadow-sm
                                                {{ $index == 0 ? 'bg-amber-100 text-amber-600 border border-amber-200' : '' }}
                                                {{ $index == 1 ? 'bg-slate-100 text-slate-500 border border-slate-200' : '' }}
                                                {{ $index == 2 ? 'bg-orange-100 text-orange-600 border border-orange-200' : '' }}
                                                {{ $index > 2 ? 'bg-gray-50 text-gray-400' : '' }}">
                                                {{ $index + 1 }}
                                            </div>

                                            @if($record->photo_url)
                                                <img src="{{ $record->photo_url }}" class="w-12 h-12 rounded-lg object-cover border border-gray-200 shadow-sm">
                                            @else
                                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-300 border border-gray-200">
                                                    <i class="fa-solid fa-camera"></i>
                                                </div>
                                            @endif

                                            <div>
                                                <h4 class="font-bold text-gray-900">{{ $record->fish_name }}</h4>
                                                <p class="text-xs text-gray-500">Złowiona przez: <span class="font-semibold text-emerald-700">{{ $record->user_name ?? 'Anonim' }}</span></p>
                                            </div>
                                        </div>

                                        <div class="text-right">
                                            <div class="text-lg font-black text-gray-800">{{ $record->weight }} <span class="text-xs font-normal text-gray-400 uppercase">kg</span></div>
                                            <div class="text-xs text-gray-400 font-medium">{{ $record->length }} cm</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-10">
                                <i class="fa-solid fa-fish-fins text-4xl text-gray-200 mb-3"></i>
                                <p class="text-gray-500">Brak zgłoszonych rekordów na tym łowisku.</p>
                                <p class="text-xs text-gray-400 mt-1">Bądź pierwszym, który pochwali się okazem!</p>
                            </div>
                        @endif
                    </div>

                    <div class="p-6 bg-emerald-50/30">
                        <h3 class="font-bold text-gray-800 mb-4">Zgłoś rekord złowionej ryby</h3>
                        
                        @if(session('success_record'))
                            <div class="mb-5 p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 font-bold rounded-xl flex items-center gap-3">
                                <i class="fa-solid fa-circle-check text-lg"></i>
                                <span>{{ session('success_record') }}</span>
                            </div>
                        @endif

                        @auth
                            @if($errors->hasAny(['fish_name', 'weight', 'length', 'caught_at', 'photo']))
                                <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-800 font-bold rounded-xl flex flex-col gap-1.5 shadow-sm text-sm">
                                    <div class="flex items-center gap-2 text-red-955 font-black uppercase text-xs tracking-wider">
                                        <i class="fa-solid fa-circle-exclamation text-base text-red-650"></i>
                                        <span>Błędy zgłoszenia rekordu</span>
                                    </div>
                                    <ul class="list-disc list-inside text-xs space-y-0.5 font-bold">
                                        @foreach(['fish_name', 'weight', 'length', 'caught_at', 'photo'] as $field)
                                            @if($errors->has($field))
                                                @foreach($errors->get($field) as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('spots.records.store', $spot->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 mb-1">Nazwa ryby *</label>
                                        <input type="text" name="fish_name" value="{{ old('fish_name') }}" required placeholder="np. Szczupak, Karp, Sandacz..." class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold text-gray-850">
                                    </div>
                                    <div class="grid grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 mb-1">Waga (kg) *</label>
                                            <input type="number" step="0.01" name="weight" value="{{ old('weight') }}" required placeholder="np. 5.2" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold text-gray-850">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 mb-1">Długość (cm) *</label>
                                            <input type="number" name="length" value="{{ old('length') }}" required placeholder="np. 75" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold text-gray-850">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 mb-1">Data złowienia *</label>
                                            <input type="date" name="caught_at" required value="{{ old('caught_at', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold text-gray-850">
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-2">Zdjęcie okazu * (Upload z dysku)</label>
                                    <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-emerald-500 transition-colors cursor-pointer bg-white" onclick="document.getElementById('file-upload').click()">
                                        <div class="space-y-2 text-center">
                                            <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-400"></i>
                                            <div class="flex text-sm text-gray-600 justify-center">
                                                <label for="file-upload" class="relative cursor-pointer rounded-md font-medium text-emerald-600 hover:text-emerald-500 focus-within:outline-none">
                                                    <span id="file-upload-text">Kliknij, aby wybrać plik</span>
                                                    <input id="file-upload" name="photo" type="file" required class="sr-only" accept="image/png, image/jpeg, image/jpg" onchange="document.getElementById('file-upload-text').innerText = this.files[0].name">
                                                </label>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, JPEG (Maks. 5MB)</p>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-2.5 px-6 rounded-lg transition text-sm shadow-md">
                                    Zgłoś rekord
                                </button>
                            </form>
                        @else
                            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 text-center">
                                <p class="text-gray-600 font-bold mb-4">Zaloguj się, aby zgłosić swój rekord złowionej ryby!</p>
                                <button onclick="openLoginModal()" class="bg-emerald-700 hover:bg-emerald-800 text-white font-black px-6 py-2.5 rounded-xl transition-all uppercase tracking-widest text-xs">
                                    Zaloguj się
                                </button>
                            </div>
                        @endauth
                    </div>
                </section>

                <section id="opinie" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-xl font-bold text-gray-800">Opinie ({{ $spot->reviews->count() }})</h2>
                        <div class="flex items-center space-x-2">
                            <span class="text-2xl font-bold text-gray-800">{{ $avgRating }}</span>
                            <span class="text-xs text-gray-500 uppercase font-semibold">Średnia ocena</span>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        @forelse($spot->reviews as $review)
                            <div class="border-b border-gray-50 pb-6 last:border-0 last:pb-0">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h4 class="font-bold text-gray-900">{{ $review->user_name ?? 'Wędkarz' }}</h4>
                                        <div class="text-amber-500 text-xs mt-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <i class="fa-solid fa-star"></i>
                                                @elseif($i - 0.5 <= $review->rating)
                                                    <i class="fa-solid fa-star-half-stroke"></i>
                                                @else
                                                    <i class="fa-regular fa-star text-gray-250"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs text-gray-400">{{ $review->created_at->format('Y-m-d') }}</span>
                                        @auth
                                            @if(Auth::user()->isAdmin())
                                                <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="admin-confirm-form inline" data-title="Usuwanie opinii" data-text="Czy na pewno chcesz bezpowrotnie usunąć opinię użytkownika {{ $review->user_name ?? 'Wędkarz' }}?" data-confirm-text="Usuń opinię" data-type="danger">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Usuń opinię">
                                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                                <p class="text-gray-600 text-sm mt-2">{{ $review->body }}</p>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">Brak opinii o tym łowisku.</p>
                        @endforelse
                    </div>

                    <div class="p-6 bg-gray-50 border-t border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4">Dodaj opinię</h3>
                        
                        @auth
                            @if(session('success_review'))
                                <div class="mb-5 p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 font-bold rounded-xl flex items-center gap-3 shadow-sm">
                                    <i class="fa-solid fa-circle-check text-lg"></i>
                                    <span>{{ session('success_review') }}</span>
                                </div>
                            @endif

                            @if($errors->hasAny(['rating', 'comment']))
                                <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-800 font-bold rounded-xl flex flex-col gap-1.5 shadow-sm text-sm">
                                    <div class="flex items-center gap-2 text-red-955 font-black uppercase text-xs tracking-wider">
                                        <i class="fa-solid fa-circle-exclamation text-base text-red-650"></i>
                                        <span>Błędy dodawania opinii</span>
                                    </div>
                                    <ul class="list-disc list-inside text-xs space-y-0.5 font-bold">
                                        @foreach($errors->all() as $error)
                                            @if(str_contains($error, 'rating') || str_contains($error, 'ocen'))
                                                <li>Wybierz ocenę (kliknij gwiazdki).</li>
                                            @elseif(str_contains($error, 'comment') || str_contains($error, 'koment'))
                                                <li>Komentarz musi mieć co najmniej 5 znaków.</li>
                                            @else
                                                <li>{{ $error }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('spots.reviews.store', $spot->id) }}" method="POST" class="space-y-5">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 mb-1">Autor opinii</label>
                                        <input type="text" value="{{ Auth::user()->name }}" disabled class="w-full px-3 py-2 bg-gray-100 border border-gray-250 rounded-lg text-sm text-gray-500 font-bold cursor-not-allowed">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 mb-2">Ocena *</label>
                                        <input type="hidden" name="rating" id="review-rating-input" value="{{ old('rating', 0) }}">
                                        <div class="flex items-center space-x-1 cursor-pointer no-select" id="review-star-container">
                                            @for($i = 1; $i <= 5; $i++)
                                                <div class="review-star text-2xl text-gray-300 hover:scale-110 transition-transform px-0.5" data-index="{{ $i }}">
                                                    <i class="fa-regular fa-star pointer-events-none"></i>
                                                </div>
                                            @endfor
                                            <span id="review-rating-display" class="ml-3 text-sm font-bold text-gray-400">Wybierz...</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1">Komentarz *</label>
                                    <textarea name="comment" rows="3" required placeholder="Podziel się swoimi doświadczeniami..." class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-bold text-gray-850">{{ old('comment') }}</textarea>
                                </div>
                                <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-2.5 px-6 rounded-lg transition text-sm shadow-md">
                                    Dodaj opinię
                                </button>
                            </form>
                        @else
                            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6 text-center">
                                <p class="text-gray-600 font-bold mb-4">Zaloguj się, aby dodać swoją opinię i ocenę!</p>
                                <button onclick="openLoginModal()" class="bg-emerald-700 hover:bg-emerald-800 text-white font-black px-6 py-2.5 rounded-xl transition-all uppercase tracking-widest text-xs">
                                    Zaloguj się
                                </button>
                            </div>
                        @endauth
                    </div>
                </section>
            </div>

            <div class="space-y-6">
                <!-- PANEL WARUNKÓW ŁOWIENIA (SYGNALIZATOR BRAN) -->
                <div class="bg-white rounded-3xl shadow-xl border border-emerald-100 p-6 sticky top-6 overflow-hidden italic">
                    <!-- Nagłówek i Wynik Brań -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight flex items-center gap-1.5"><i class="fa-solid fa-square-poll-vertical text-emerald-600"></i> Warunki brań</h3>
                            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider mt-1" id="live-date-label">
                                <i class="fa-regular fa-clock mr-1 text-emerald-500"></i> <span id="live-time">Ładowanie...</span>
                            </p>
                        </div>
                        <div class="flex flex-col items-end">
                            <span id="bite-score-badge" class="bg-gray-100 text-gray-400 font-black px-4 py-2 rounded-2xl shadow-sm text-sm mb-1 transition-all duration-500 min-w-[55px] text-center">--/100</span>
                            <span id="bite-rating-label" class="text-gray-400 text-xs font-black uppercase tracking-widest">Wczytywanie</span>
                        </div>
                    </div>

                    <!-- Informacja o Lokalizacji (GPS / Łowisko) -->
                    <div class="mb-5 p-3.5 bg-emerald-50/40 rounded-2xl border border-emerald-100/50 flex items-center justify-between">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-xl bg-emerald-700/10 flex items-center justify-center text-emerald-700 shrink-0 shadow-sm">
                                <i class="fa-solid fa-location-crosshairs text-sm" id="loc-icon"></i>
                            </div>
                            <div class="leading-tight">
                                <p class="text-[9px] text-gray-400 font-black uppercase tracking-wider">Lokalizacja danych</p>
                                <p class="text-xs font-black text-emerald-950 uppercase" id="loc-name-label">Łowisko: {{ $spot->name }}</p>
                            </div>
                        </div>
                        <button type="button" onclick="toggleLocationSource()" class="bg-white hover:bg-emerald-50 text-emerald-700 font-black text-[9px] px-2.5 py-1.5 rounded-xl border border-emerald-200 transition-all uppercase tracking-widest shadow-sm">
                            Zmień na GPS
                        </button>
                    </div>

                    <!-- Faza Księżyca -->
                    <div class="mb-4 pb-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <div class="flex items-center space-x-2 text-gray-800 font-bold mb-1 text-xs uppercase tracking-wider">
                                <i class="fa-solid fa-moon text-purple-600 text-sm"></i> <span>Faza księżyca</span>
                            </div>
                            <p class="text-xs text-gray-600 font-bold" id="moon-phase-name">Obliczanie...</p>
                            <p class="text-[9px] text-purple-500 font-black uppercase tracking-widest mt-1" id="moon-phase-desc">Analizowanie orbity</p>
                        </div>
                        <div class="text-3xl text-purple-600" id="moon-phase-icon"><i class="fa-solid fa-circle-notch animate-spin text-lg"></i></div>
                    </div>

                    <!-- Ciśnienie Atmosferyczne -->
                    <div class="mb-4 pb-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <div class="flex items-center space-x-2 text-gray-800 font-bold mb-1 text-xs uppercase tracking-wider">
                                <i class="fa-solid fa-gauge-high text-blue-600 text-sm"></i> <span>Ciśnienie (Live)</span>
                            </div>
                            <p class="text-xs font-bold transition-all" id="pressure-trend">Ładowanie...</p>
                            <p class="text-[9px] text-blue-500 font-black uppercase tracking-widest mt-1" id="pressure-desc">Pobieranie danych</p>
                        </div>
                        <div class="text-lg font-black text-gray-900" id="pressure-val">-- <span class="text-[10px] text-gray-400 font-bold">hPa</span></div>
                    </div>

                    <!-- Pogoda i Temperatura -->
                    <div class="mb-5 flex items-center justify-between">
                        <div>
                            <div class="flex items-center space-x-2 text-gray-800 font-bold mb-1 text-xs uppercase tracking-wider">
                                <i class="fa-solid fa-cloud-sun text-amber-500 text-sm"></i> <span>Warunki pogodowe</span>
                            </div>
                            <p class="text-xs text-gray-600 font-bold" id="weather-desc">Wczytywanie...</p>
                            <p class="text-[9px] text-amber-600 font-black uppercase tracking-widest mt-1">Temperatura rzeczywista</p>
                        </div>
                        <div class="flex items-center text-lg font-black text-gray-900" id="weather-temp">
                            <i class="fa-solid fa-circle-notch animate-spin text-gray-300 mr-2 text-sm"></i> --°C
                        </div>
                    </div>

                    <!-- Wykres Aktywności -->
                    <div class="mt-6 pt-5 border-t border-gray-100">
                        <h4 class="font-black text-gray-900 text-xs uppercase tracking-widest mb-3 flex items-center gap-1.5"><i class="fa-solid fa-chart-line text-emerald-600"></i> Dobowy wykres brań</h4>
                        <div class="bg-emerald-950 rounded-2xl p-4 relative overflow-hidden h-32 border border-emerald-900/50 flex flex-col justify-between">
                            <!-- SVG Wykres -->
                            <div class="absolute inset-0 w-full h-full pt-6 pb-4 px-2">
                                <svg class="w-full h-full" viewBox="0 0 100 30" preserveAspectRatio="none" id="bite-chart-svg">
                                    <!-- Dynamiczny gradient wykresu -->
                                    <defs>
                                        <linearGradient id="chart-grad" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stop-color="#10b981" stop-opacity="0.3"></stop>
                                            <stop offset="100%" stop-color="#10b981" stop-opacity="0.0"></stop>
                                        </linearGradient>
                                    </defs>
                                    <!-- Dynamiczne linie zostaną wstawione przez JS -->
                                    <path d="M 0,30 Q 50,30 100,30 L 100,30 L 0,30 Z" fill="url(#chart-grad)" id="bite-chart-fill"></path>
                                    <path d="M 0,30 Q 50,30 100,30" fill="none" stroke="#10b981" stroke-width="1.5" id="bite-chart-line"></path>
                                    <!-- Dynamiczny punkt aktualnej godziny -->
                                    <circle cx="-10" cy="-10" r="1.8" fill="#f59e0b" id="bite-chart-dot" class="animate-pulse"></circle>
                                </svg>
                            </div>

                            <div class="absolute top-2 left-4 w-full flex justify-between pr-8 text-[8px] font-black text-emerald-500/50 uppercase tracking-widest">
                                <span>Rano</span><span>Południe</span><span>Wieczór</span>
                            </div>

                            <div class="absolute bottom-1 left-0 w-full px-4 flex justify-between text-emerald-400/30 text-[8px] font-bold">
                                <span>06:00</span><span>12:00</span><span>18:00</span><span>22:00</span>
                            </div>
                        </div>

                        <!-- Najlepsze Godziny dzisiaj -->
                        <div class="bg-emerald-50/30 p-3 rounded-2xl border border-emerald-100/50 mt-3 text-[10px] text-center font-bold">
                            <span class="text-emerald-800 uppercase tracking-wider"><i class="fa-solid fa-award mr-1"></i> Złote godziny brań dziś:</span> 
                            <span class="text-emerald-950 font-black tracking-wide" id="golden-hours-val">obliczanie...</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <footer class="bg-emerald-950 text-emerald-200 py-6 mt-10">
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

    @auth
        @if(Auth::user()->isAdmin())
            <div id="edit-spot-modal" class="fixed inset-0 z-[9999] hidden opacity-0 transition-all duration-300 flex items-center justify-center p-4">
                <!-- Zamazane tło -->
                <div class="absolute inset-0 bg-emerald-950/70 backdrop-blur-md cursor-pointer" onclick="closeEditSpotModal()"></div>

                <!-- Karta Modala -->
                <div class="relative z-10 w-full max-w-[600px] transform scale-95 transition-all duration-300 flex flex-col" id="edit-spot-card">
                    <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden w-full max-h-[90vh] flex flex-col">

                        <!-- Nagłówek Modala -->
                        <div class="bg-emerald-900 pt-8 pb-6 text-center relative overflow-hidden shrink-0">
                            <div class="absolute inset-0 z-0">
                                <img src="https://images.unsplash.com/photo-1506125840744-167167210587?q=80&w=1200" class="w-full h-full object-cover blur-[3px] opacity-40 scale-110">
                            </div>
                            <div class="relative z-10">
                                <h2 class="text-2xl font-black text-white tracking-tight italic uppercase drop-shadow-md">Edytuj Łowisko</h2>
                                <p class="text-emerald-200 text-[10px] font-bold opacity-90 mt-1 uppercase tracking-widest">Zmień parametry łowiska w bazie</p>
                            </div>
                        </div>

                        <!-- Formularz w scrollowalnej sekcji -->
                        <div class="p-8 md:p-10 overflow-y-auto custom-scrollbar flex-grow text-left">
                            
                            @if($errors->hasAny(['name', 'type', 'city', 'region', 'description', 'lat', 'lng', 'main_image_url', 'main_image', 'amenities', 'fish_species_check']))
                                <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-800 font-bold rounded-2xl flex flex-col gap-1.5 shadow-sm text-sm">
                                    <div class="flex items-center gap-2 text-red-955 font-black uppercase text-xs tracking-wider">
                                        <i class="fa-solid fa-circle-exclamation text-base text-red-650"></i>
                                        <span>Błędy walidacji</span>
                                    </div>
                                    <ul class="list-disc list-inside text-xs space-y-0.5 font-bold">
                                        @foreach(['name', 'type', 'city', 'region', 'description', 'lat', 'lng', 'main_image_url', 'main_image', 'amenities', 'fish_species_check'] as $field)
                                            @if($errors->has($field))
                                                @foreach($errors->get($field) as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form id="form-edit-spot" method="POST" action="{{ route('admin.spots.update', $spot->id) }}" enctype="multipart/form-data" class="space-y-5 italic">
                                @csrf
                                @method('PUT')

                                <!-- Nazwa łowiska -->
                                <div>
                                    <label class="block text-emerald-800 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-heading mr-1"></i> Nazwa Łowiska *</label>
                                    <input type="text" name="name" value="{{ old('name', $spot->name) }}" required class="w-full bg-emerald-50/50 focus:bg-white border-2 border-emerald-100 focus:border-emerald-500 rounded-2xl px-5 py-3 text-gray-800 font-bold placeholder-gray-400 focus:outline-none transition-all">
                                </div>

                                <!-- Typ wody -->
                                <div>
                                    <label class="block text-emerald-800 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-water mr-1"></i> Typ wody *</label>
                                    <select name="type" required class="w-full bg-emerald-50/50 focus:bg-white border-2 border-emerald-100 focus:border-emerald-500 rounded-2xl px-5 py-3 text-gray-800 font-bold focus:outline-none transition-all">
                                        <option value="jezioro" {{ old('type', $spot->type) == 'jezioro' ? 'selected' : '' }}>Jezioro</option>
                                        <option value="rzeka" {{ old('type', $spot->type) == 'rzeka' ? 'selected' : '' }}>Rzeka</option>
                                        <option value="zalew" {{ old('type', $spot->type) == 'zalew' ? 'selected' : '' }}>Zalew</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <!-- Miejscowość -->
                                    <div>
                                        <label class="block text-emerald-800 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-city mr-1"></i> Miejscowość *</label>
                                        <input type="text" name="city" value="{{ old('city', $spot->city) }}" required class="w-full bg-emerald-50/50 focus:bg-white border-2 border-emerald-100 focus:border-emerald-500 rounded-2xl px-5 py-3 text-gray-800 font-bold placeholder-gray-400 focus:outline-none transition-all">
                                    </div>

                                    <!-- Województwo -->
                                    <div>
                                        <label class="block text-emerald-800 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-map mr-1"></i> Województwo *</label>
                                        <input type="text" name="region" value="{{ old('region', $spot->region) }}" required class="w-full bg-emerald-50/50 focus:bg-white border-2 border-emerald-100 focus:border-emerald-500 rounded-2xl px-5 py-3 text-gray-800 font-bold placeholder-gray-400 focus:outline-none transition-all">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <!-- Szerokość geo (lat) -->
                                    <div>
                                        <label class="block text-emerald-800 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-location-crosshairs mr-1"></i> Szerokość (Lat) *</label>
                                        <input type="number" step="any" name="lat" value="{{ old('lat', $spot->lat) }}" required class="w-full bg-emerald-50/50 focus:bg-white border-2 border-emerald-100 focus:border-emerald-500 rounded-2xl px-5 py-3 text-gray-800 font-bold placeholder-gray-400 focus:outline-none transition-all">
                                    </div>

                                    <!-- Długość geo (lng) -->
                                    <div>
                                        <label class="block text-emerald-800 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-location-crosshairs mr-1"></i> Długość (Lng) *</label>
                                        <input type="number" step="any" name="lng" value="{{ old('lng', $spot->lng) }}" required class="w-full bg-emerald-50/50 focus:bg-white border-2 border-emerald-100 focus:border-emerald-500 rounded-2xl px-5 py-3 text-gray-800 font-bold placeholder-gray-400 focus:outline-none transition-all">
                                    </div>
                                </div>

                                <!-- Opis łowiska -->
                                <div>
                                    <label class="block text-emerald-800 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-quote-left mr-1"></i> Opis Łowiska</label>
                                    <textarea name="description" rows="3" class="w-full bg-emerald-50/50 focus:bg-white border-2 border-emerald-100 focus:border-emerald-500 rounded-2xl px-5 py-3 text-gray-800 font-bold placeholder-gray-400 focus:outline-none transition-all">{{ old('description', $spot->description) }}</textarea>
                                </div>

                                <!-- URL zdjęcia głównego -->
                                <div>
                                    <label class="block text-emerald-800 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-link mr-1"></i> URL Zdjęcia Głównego *</label>
                                    <input type="text" name="main_image_url" value="{{ old('main_image_url', $spot->main_image_url) }}" required class="w-full bg-emerald-50/50 focus:bg-white border-2 border-emerald-100 focus:border-emerald-500 rounded-2xl px-5 py-3 text-gray-800 font-bold placeholder-gray-400 focus:outline-none transition-all">
                                </div>

                                <!-- Nowe zdjęcie łowiska (opcjonalne) -->
                                <div>
                                    <label class="block text-emerald-800 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-image mr-1"></i> Zmień Zdjęcie Łowiska (Opcjonalne)</label>
                                    <div class="flex justify-center px-6 pt-5 pb-5 border-2 border-emerald-100 border-dashed rounded-2xl hover:border-emerald-500 transition-colors cursor-pointer bg-emerald-50/30" onclick="document.getElementById('edit-spot-image-upload').click()">
                                        <div class="space-y-1 text-center">
                                            <i class="fa-solid fa-cloud-arrow-up text-2xl text-emerald-600"></i>
                                            <div class="flex text-sm text-gray-600 justify-center">
                                                <span id="edit-spot-image-text" class="font-bold text-emerald-700 hover:text-emerald-800">Kliknij, aby wybrać plik</span>
                                                <input id="edit-spot-image-upload" name="main_image" type="file" class="sr-only" accept="image/*" onchange="document.getElementById('edit-spot-image-text').innerText = this.files[0].name; document.getElementById('edit-spot-image-text').className = 'font-bold text-gray-800'">
                                            </div>
                                            <p class="text-[9px] text-gray-400 font-medium uppercase tracking-wide">Maks. 5MB (pozostaw puste, aby nie zmieniać zdjęcia)</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Udogodnienia -->
                                <div>
                                    <label class="block text-emerald-800 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-circle-check mr-1"></i> Udogodnienia</label>
                                    <div class="grid grid-cols-2 gap-3 bg-emerald-50/30 p-4 rounded-2xl border border-emerald-100 text-left">
                                        @foreach($allAmenities as $amenity)
                                            @php
                                                $oldAmenities = old('amenities');
                                                $hasAmenity = is_array($oldAmenities) ? in_array($amenity->id, $oldAmenities) : $spot->amenities->contains($amenity->id);
                                            @endphp
                                            <label class="flex items-center space-x-2 text-sm text-gray-700 cursor-pointer">
                                                <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" 
                                                    {{ $hasAmenity ? 'checked' : '' }}
                                                    class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                                <span>{{ $amenity->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Gatunki ryb i procentowy udział -->
                                <div>
                                    <label class="block text-emerald-800 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-fish mr-1"></i> Struktura zarybienia (ryby i %)</label>
                                    <div class="bg-emerald-50/30 p-4 rounded-2xl border border-emerald-100 max-h-60 overflow-y-auto custom-scrollbar space-y-2 text-left">
                                        @foreach($allSpecies as $species)
                                            @php
                                                $oldCheck = old('fish_species_check');
                                                $isChecked = is_array($oldCheck) ? in_array($species->id, $oldCheck) : $spot->fish_species->contains($species->id);
                                                $oldPct = old('fish_species_pct.' . $species->id);
                                                $dbSpecies = $spot->fish_species->firstWhere('id', $species->id);
                                                $dbPct = $dbSpecies ? ($dbSpecies->pivot->percentage ?? 0) : 0;
                                                $pct = !is_null($oldPct) ? (int)$oldPct : $dbPct;
                                            @endphp
                                            <div class="flex items-center justify-between border-b border-emerald-100/50 py-1.5 last:border-0">
                                                <label class="flex items-center space-x-2 text-sm text-gray-700 cursor-pointer select-none">
                                                    <input type="checkbox" name="fish_species_check[]" value="{{ $species->id }}" 
                                                        {{ $isChecked ? 'checked' : '' }}
                                                        class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                                    <span>{{ $species->name }}</span>
                                                </label>
                                                <div class="flex items-center space-x-1">
                                                    <input type="number" name="fish_species_pct[{{ $species->id }}]" value="{{ $pct }}" min="0" max="100" 
                                                        class="w-16 px-2 py-1 text-xs border border-gray-300 rounded focus:ring-emerald-500 focus:border-emerald-500 text-right font-bold text-gray-800">
                                                    <span class="text-xs text-gray-500 font-bold">%</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Przyciski -->
                                <div class="flex gap-4 pt-2">
                                    <button type="button" onclick="closeEditSpotModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-600 font-black px-6 py-4 rounded-2xl transition-all uppercase tracking-widest text-xs">
                                        Anuluj
                                    </button>
                                    <button type="submit" class="flex-1 bg-emerald-700 hover:bg-emerald-800 text-white font-black px-6 py-4 rounded-2xl transition-all uppercase tracking-widest text-xs shadow-md">
                                        Zapisz Zmiany
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endauth

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
            // opinie w formie gwiazdek
            const starContainer = document.getElementById('review-star-container');
            if (starContainer) {
                const stars = starContainer.querySelectorAll('.review-star');
                const hiddenInput = document.getElementById('review-rating-input');
                const display = document.getElementById('review-rating-display');
                let currentRating = parseFloat(hiddenInput.value) || 0;

                // Rozszerzony słownik o połówki ocen
                const textLabels = {
                    0.5: "0.5 - Tragedia", 1: "1.0 - Omijać", 1.5: "1.5 - Bardzo słabe",
                    2: "2.0 - Słabe", 2.5: "2.5 - Poniżej średniej", 3: "3.0 - Przeciętne",
                    3.5: "3.5 - Całkiem niezłe", 4: "4.0 - Bardzo dobre", 4.5: "4.5 - Rewelacyjne", 5: "5.0 - Wybitne"
                };

                function renderStars(value) {
                    stars.forEach((star, index) => {
                        const starIndex = index + 1;
                        const icon = star.querySelector('i');
                        icon.className = 'pointer-events-none transition-colors duration-150';
                        if (value >= starIndex) {
                            icon.classList.add('fa-solid', 'fa-star', 'text-amber-500');
                        } else if (value === starIndex - 0.5) {
                            icon.classList.add('fa-solid', 'fa-star-half-stroke', 'text-amber-500');
                        } else {
                            icon.classList.add('fa-regular', 'fa-star', 'text-gray-300');
                        }
                    });
                }

                stars.forEach(star => {
                    star.addEventListener('mousemove', function(e) {
                        const rect = this.getBoundingClientRect();
                        const isHalf = (e.clientX - rect.left) < rect.width / 2;
                        renderStars(isHalf ? parseFloat(this.getAttribute('data-index')) - 0.5 : parseFloat(this.getAttribute('data-index')));
                    });

                    star.addEventListener('click', function(e) {
                        const rect = this.getBoundingClientRect();
                        const isHalf = (e.clientX - rect.left) < rect.width / 2;
                        currentRating = isHalf ? parseFloat(this.getAttribute('data-index')) - 0.5 : parseFloat(this.getAttribute('data-index'));
                        hiddenInput.value = currentRating;
                        display.textContent = textLabels[currentRating];
                        display.classList.remove('text-gray-400');
                        display.classList.add('text-emerald-600');
                        renderStars(currentRating);
                    });
                });

                starContainer.addEventListener('mouseleave', () => renderStars(currentRating));

                // Uruchomienie na starcie przy istniejącym ratingu (np. przy old())
                if (currentRating > 0) {
                    renderStars(currentRating);
                    display.textContent = textLabels[currentRating];
                    display.classList.remove('text-gray-400');
                    display.classList.add('text-emerald-600');
                }
            }

            // upload zdjęcia w formularzu zgłaszania rekordu
            const fileUpload = document.getElementById('file-upload');
            fileUpload.addEventListener('change', function() {
                if(this.files && this.files.length > 0) {
                    const fileName = this.files[0].name;
                    this.previousElementSibling.textContent = "Wybrano: " + fileName;
                    this.previousElementSibling.classList.replace('text-emerald-600', 'text-amber-600');
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

    // === LIVE FORECAST SYGNALIZATOR BRAN ===
    const spotLat = parseFloat("{{ str_replace(',', '.', $spot->lat ?? 52.23) }}");
    const spotLng = parseFloat("{{ str_replace(',', '.', $spot->lng ?? 21.01) }}");
    const spotName = "{{ $spot->name }}";
    let activeLat = spotLat;
    let activeLng = spotLng;
    let locationSource = 'spot'; // 'spot' or 'gps'

    // Moon phase calculator
    function getMoonPhase(d) {
        let year = d.getFullYear();
        let month = d.getMonth() + 1;
        let day = d.getDate();
        
        if (month < 3) { year--; month += 12; }
        month++;
        let c = 365.25 * year;
        let e = 30.6 * month;
        let jd = c + e + day - 694039.09;
        jd /= 29.5305882;
        let b = parseInt(jd);
        jd -= b;
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
        0: { desc: "Słonecznie (Czyste niebo)", icon: '<i class="fa-solid fa-sun text-amber-500 mr-2"></i>' },
        1: { desc: "Głównie bezchmurnie", icon: '<i class="fa-solid fa-cloud-sun text-amber-400 mr-2"></i>' },
        2: { desc: "Częściowe zachmurzenie", icon: '<i class="fa-solid fa-cloud-sun text-gray-400 mr-2"></i>' },
        3: { desc: "Całkowite zachmurzenie", icon: '<i class="fa-solid fa-cloud text-gray-500 mr-2"></i>' },
        45: { desc: "Mgła", icon: '<i class="fa-solid fa-smog text-gray-300 mr-2"></i>' },
        48: { desc: "Oszronienie", icon: '<i class="fa-solid fa-snowflake text-blue-200 mr-2"></i>' },
        51: { desc: "Lekka mżawka", icon: '<i class="fa-solid fa-cloud-rain text-blue-400 mr-2"></i>' },
        53: { desc: "Umiarkowana mżawka", icon: '<i class="fa-solid fa-cloud-rain text-blue-500 mr-2"></i>' },
        55: { desc: "Intensywna mżawka", icon: '<i class="fa-solid fa-cloud-showers-heavy text-blue-600 mr-2"></i>' },
        61: { desc: "Lekki deszcz", icon: '<i class="fa-solid fa-cloud-rain text-blue-400 mr-2"></i>' },
        63: { desc: "Umiarkowany deszcz", icon: '<i class="fa-solid fa-cloud-showers-heavy text-blue-500 mr-2"></i>' },
        65: { desc: "Ulewny deszcz", icon: '<i class="fa-solid fa-cloud-showers-water text-blue-600 mr-2"></i>' },
        71: { desc: "Lekki opad śniegu", icon: '<i class="fa-solid fa-snowflake text-blue-100 mr-2"></i>' },
        73: { desc: "Umiarkowany śnieg", icon: '<i class="fa-solid fa-snowflake text-blue-200 mr-2"></i>' },
        75: { desc: "Silna śnieżyca", icon: '<i class="fa-solid fa-snowflake text-blue-300 mr-2"></i>' },
        80: { desc: "Lekki przelotny deszcz", icon: '<i class="fa-solid fa-cloud-rain text-blue-400 mr-2"></i>' },
        81: { desc: "Umiarkowane ulewy", icon: '<i class="fa-solid fa-cloud-showers-heavy text-blue-500 mr-2"></i>' },
        82: { desc: "Gwałtowne ulewy", icon: '<i class="fa-solid fa-cloud-showers-water text-blue-700 mr-2"></i>' },
        95: { desc: "Burza", icon: '<i class="fa-solid fa-cloud-bolt text-amber-500 mr-2 animate-pulse"></i>' }
    };

    function getWeatherInfo(code) {
        return weatherCodes[code] || { desc: "Umiarkowane zachmurzenie", icon: '<i class="fa-solid fa-cloud text-gray-400 mr-2"></i>' };
    }

    async function loadBiteForecast() {
        const timeEl = document.getElementById('live-time');
        const badgeEl = document.getElementById('bite-score-badge');
        const labelEl = document.getElementById('bite-rating-label');
        const moonNameEl = document.getElementById('moon-phase-name');
        const moonDescEl = document.getElementById('moon-phase-desc');
        const moonIconEl = document.getElementById('moon-phase-icon');
        const pressureTrendEl = document.getElementById('pressure-trend');
        const pressureDescEl = document.getElementById('pressure-desc');
        const pressureValEl = document.getElementById('pressure-val');
        const weatherDescEl = document.getElementById('weather-desc');
        const weatherTempEl = document.getElementById('weather-temp');
        const goldenHoursEl = document.getElementById('golden-hours-val');
        const locNameEl = document.getElementById('loc-name-label');

        if (!timeEl) return; // Zapobiega błędom na innych podstronach

        // Set dynamic date
        const now = new Date();
        const days = ["Niedziela", "Poniedziałek", "Wtorek", "Środa", "Czwartek", "Piątek", "Sobota"];
        const months = ["stycznia", "lutego", "marca", "kwietnia", "maja", "czerwca", "lipca", "sierpnia", "września", "października", "listopada", "grudnia"];
        timeEl.innerHTML = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} o ${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`;

        locNameEl.innerText = locationSource === 'spot' ? `Łowisko: ${spotName}` : "Twoja lokalizacja (GPS)";

        try {
            const url = `https://api.open-meteo.com/v1/forecast?latitude=${activeLat}&longitude=${activeLng}&current=temperature_2m,weather_code,pressure_msl&hourly=temperature_2m,weather_code,pressure_msl&timezone=auto`;
            const res = await fetch(url);
            const data = await res.json();

            if (!data.current) throw new Error("Błąd pobierania danych");

            const current = data.current;
            const temp = current.temperature_2m;
            const pressure = current.pressure_msl;
            const weatherCode = current.weather_code;
            
            // Pressure trend (obecna godzina vs 3 godziny temu)
            let pressureTrendValue = 0;
            if (data.hourly && data.hourly.pressure_msl) {
                const hourIndex = now.getHours();
                const pastPressure = data.hourly.pressure_msl[Math.max(0, hourIndex - 3)];
                pressureTrendValue = pressure - pastPressure;
            }

            // Faza księżyca
            const moon = getMoonPhase(now);
            moonNameEl.innerText = moon.name;
            moonDescEl.innerText = moon.desc;
            moonIconEl.innerHTML = moon.icon;

            // Ciśnienie
            pressureValEl.innerHTML = `${Math.round(pressure)} <span class="text-[10px] text-gray-400 font-bold">hPa</span>`;
            if (pressureTrendValue > 0.4) {
                pressureTrendEl.innerHTML = `<span class="text-red-500 font-extrabold"><i class="fa-solid fa-arrow-trend-up"></i> +${pressureTrendValue.toFixed(1)} hPa/3h</span>`;
                pressureDescEl.innerText = "Gwałtowny wzrost (Słabsze brania)";
            } else if (pressureTrendValue < -0.4) {
                pressureTrendEl.innerHTML = `<span class="text-emerald-500 font-extrabold"><i class="fa-solid fa-arrow-trend-down"></i> ${pressureTrendValue.toFixed(1)} hPa/3h</span>`;
                pressureDescEl.innerText = "Spadek (Aktywność wzrasta!)";
            } else {
                pressureTrendEl.innerHTML = `<span class="text-blue-500 font-extrabold"><i class="fa-solid fa-equals"></i> Stabilne (${pressureTrendValue >= 0 ? '+' : ''}${pressureTrendValue.toFixed(1)})</span>`;
                pressureDescEl.innerText = "Stabilne optymalne warunki";
            }

            // Pogoda
            const wInfo = getWeatherInfo(weatherCode);
            weatherDescEl.innerText = wInfo.desc;
            weatherTempEl.innerHTML = `${wInfo.icon} ${temp.toFixed(1)}°C`;

            // Obliczenie wskaźnika brań
            const currentHour = now.getHours();
            const score = calculateDynamicBite(temp, pressure, pressureTrendValue, moon.score, weatherCode, currentHour);

            badgeEl.innerText = `${score}/100`;
            badgeEl.className = "text-white font-black px-4 py-2 rounded-2xl shadow-sm text-sm mb-1 transition-all duration-500 min-w-[55px] text-center";
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

            // Generowanie wykresu dobowego
            if (data.hourly) {
                let hourlyScores = [];
                let bestHours = [];
                
                const forecastHours = [6, 8, 10, 12, 14, 16, 18, 20, 22];
                forecastHours.forEach(h => {
                    const hTemp = data.hourly.temperature_2m[h] || temp;
                    const hPressure = data.hourly.pressure_msl[h] || pressure;
                    const hCode = data.hourly.weather_code[h] || weatherCode;
                    const hScore = calculateDynamicBite(hTemp, hPressure, pressureTrendValue, moon.score, hCode, h);
                    hourlyScores.push({ hour: h, score: hScore });
                    
                    if (hScore >= 65) {
                        bestHours.push(`${h}:00`);
                    }
                });

                goldenHoursEl.innerText = bestHours.length > 0 ? bestHours.slice(0, 3).join(", ") : "Dziś brak wybitnych godzin";

                // Rysowanie wykresu spline SVG
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
                        const cpX1 = prev.x + (p.x - prev.x) / 2;
                        const cpY1 = prev.y;
                        const cpX2 = prev.x + (p.x - prev.x) / 2;
                        const cpY2 = p.y;
                        pathD += `C ${cpX1.toFixed(1)},${cpY1.toFixed(1)} ${cpX2.toFixed(1)},${cpY2.toFixed(1)} ${p.x.toFixed(1)},${p.y.toFixed(1)} `;
                    }
                });
                
                const fillD = pathD + `L ${width},${height} L 0,${height} Z`;
                
                document.getElementById('bite-chart-line').setAttribute('d', pathD);
                document.getElementById('bite-chart-fill').setAttribute('d', fillD);

                // Pozycja pulsującej kropki aktualnej godziny
                const currentHourIdx = forecastHours.findIndex(h => h >= currentHour);
                if (currentHourIdx !== -1) {
                    const targetPoint = points[currentHourIdx];
                    document.getElementById('bite-chart-dot').setAttribute('cx', targetPoint.x);
                    document.getElementById('bite-chart-dot').setAttribute('cy', targetPoint.y);
                } else {
                    document.getElementById('bite-chart-dot').setAttribute('cx', -10);
                }
            }

        } catch (e) {
            console.error(e);
            timeEl.innerText = "Błąd pobierania danych";
            badgeEl.innerText = "ERR";
            badgeEl.className = "bg-red-500 text-white font-black px-4 py-2 rounded-2xl shadow-sm text-sm mb-1";
            labelEl.innerText = "Brak połączenia";
        }
    }

    function calculateDynamicBite(temp, pressure, pressureTrend, moonScore, weatherCode, hour) {
        let score = 50;

        // 1. Temperatura
        if (temp >= 12 && temp <= 19) score += 15;
        else if (temp < 6 || temp > 28) score -= 15;
        else score += 5;

        // 2. Ciśnienie
        if (pressure >= 1010 && pressure <= 1020) score += 15;
        else if (pressure < 995 || pressure > 1030) score -= 20;
        else score += 5;

        // 3. Trend ciśnienia
        if (Math.abs(pressureTrend) < 0.4) score += 10;
        else if (pressureTrend < 0) score += 15;
        else score -= 10;

        // 4. Księżyc
        score += (moonScore - 50) * 0.3;

        // 5. Warunki pogodowe
        if ([1, 2, 51, 61].includes(weatherCode)) score += 12;
        else if ([80, 81, 82, 95].includes(weatherCode)) score -= 22;

        // 6. Pora dnia (Złote godziny)
        if ((hour >= 5 && hour <= 8) || (hour >= 18 && hour <= 21)) score += 20;
        else if (hour >= 11 && hour <= 15) score -= 10;

        return Math.max(8, Math.min(96, Math.round(score)));
    }

    // Obsługa geolokalizacji
    window.toggleLocationSource = function() {
        const btn = document.querySelector('button[onclick="toggleLocationSource()"]');
        if (locationSource === 'spot') {
            if (navigator.geolocation) {
                btn.innerText = "Czekaj...";
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        activeLat = position.coords.latitude;
                        activeLng = position.coords.longitude;
                        locationSource = 'gps';
                        btn.innerText = "Zmień na Łowisko";
                        document.getElementById('loc-icon').className = "fa-solid fa-street-view animate-bounce text-sm";
                        loadBiteForecast();
                    },
                    (error) => {
                        alert("Nie udało się pobrać Twojej lokalizacji GPS. Sprawdź uprawnienia w przeglądarce.");
                        btn.innerText = "Zmień na GPS";
                    }
                );
            } else {
                alert("Geolokalizacja nie jest wspierana przez Twoją przeglądarkę.");
            }
        } else {
            activeLat = spotLat;
            activeLng = spotLng;
            locationSource = 'spot';
            btn.innerText = "Zmień na GPS";
            document.getElementById('loc-icon').className = "fa-solid fa-location-crosshairs text-sm";
            loadBiteForecast();
        }
    };

    // Uruchomienie na starcie
    window.addEventListener('DOMContentLoaded', () => {
        loadBiteForecast();
        setInterval(loadBiteForecast, 60000);
    });

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
            @if($errors->hasAny(['email', 'password']))
                setTimeout(window.openLoginModal, 500);
            @else
                setTimeout(window.openEditSpotModal, 500);
            @endif
        });
    @endif

    window.addEventListener('DOMContentLoaded', () => {
        const editForm = document.getElementById('form-edit-spot');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                const checkedBoxes = editForm.querySelectorAll('input[name="fish_species_check[]"]:checked');
                let totalPct = 0;
                checkedBoxes.forEach(cb => {
                    const pctInput = editForm.querySelector(`input[name="fish_species_pct[${cb.value}]"]`);
                    if (pctInput) {
                        const pctVal = parseInt(pctInput.value) || 0;
                        totalPct += Math.max(0, Math.min(100, pctVal));
                    }
                });
                
                if (totalPct > 100) {
                    e.preventDefault();
                    alert(`Suma udziałów procentowych ryb nie może przekraczać 100%! Obecnie wprowadzono: ${totalPct}%.`);
                }
            });
        }
    });

    window.openEditSpotModal = function() {
        const modal = document.getElementById('edit-spot-modal');
        const card = document.getElementById('edit-spot-card');
        if (modal && card) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                card.classList.replace('scale-95', 'scale-100');
            }, 20);
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeEditSpotModal = function() {
        const modal = document.getElementById('edit-spot-modal');
        const card = document.getElementById('edit-spot-card');
        if (modal && card) {
            modal.classList.add('opacity-0');
            card.classList.replace('scale-100', 'scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 300);
        }
    };

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

            // Walidacja struktury zarybienia po stronie klienta dla edycji
            const editForm = document.getElementById('form-edit-spot');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    const checkedBoxes = editForm.querySelectorAll('input[name="fish_species_check[]"]:checked');
                    let totalPct = 0;
                    checkedBoxes.forEach(cb => {
                        const pctInput = editForm.querySelector(`input[name="fish_species_pct[${cb.value}]"]`);
                        if (pctInput) {
                            const pctVal = parseInt(pctInput.value) || 0;
                            totalPct += Math.max(0, Math.min(100, pctVal));
                        }
                    });
                    
                    if (totalPct > 100) {
                        e.preventDefault();
                        alert(`Suma udziałów procentowych ryb nie może przekraczać 100%! Obecnie wprowadzono: ${totalPct}%.`);
                    }
                });
            }

            // Automatyczne zaznaczanie/odznaczanie kwadratu po lewej w zależności od wartości procentowej
            document.body.addEventListener('input', function(e) {
                if (e.target && e.target.name && e.target.name.startsWith('fish_species_pct[')) {
                    const value = parseInt(e.target.value) || 0;
                    const row = e.target.closest('.flex.items-center.justify-between');
                    if (row) {
                        const checkbox = row.querySelector('input[type="checkbox"][name="fish_species_check[]"]');
                        if (checkbox) {
                            checkbox.checked = value > 0;
                        }
                    }
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
