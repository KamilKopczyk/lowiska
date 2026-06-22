@extends('layouts.fishing')

@section('title', 'Panel Administratora - Wędkarski Radar')

@section('content')
<main class="container mx-auto px-4 py-10 flex-grow max-w-5xl">

    <!-- SUKCES KOMUNIKAT -->
    @if(session('success'))
        <div id="admin-success-toast" class="fixed bottom-5 right-5 z-[99999] max-w-md bg-white border border-emerald-100 rounded-3xl p-5 shadow-2xl flex items-center gap-4 transition-all duration-500 transform translate-y-20 opacity-0">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 shadow-inner">
                <i class="fa-solid fa-circle-check text-xl"></i>
            </div>
            <div class="flex-grow text-left">
                <div class="text-[10px] text-gray-400 font-extrabold uppercase tracking-widest">Akcja wykonana pomyślnie</div>
                <div class="text-sm font-bold text-gray-805 mt-0.5">{{ session('success') }}</div>
            </div>
            <button type="button" onclick="closeAdminToast()" class="text-gray-400 hover:text-gray-600 transition ml-2">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <script>
            window.addEventListener('load', () => {
                const toast = document.getElementById('admin-success-toast');
                if (toast) {
                    setTimeout(() => {
                        toast.classList.remove('translate-y-20', 'opacity-0');
                    }, 100);
                    setTimeout(() => {
                        closeAdminToast();
                    }, 6000);
                }
            });
            function closeAdminToast() {
                const toast = document.getElementById('admin-success-toast');
                if (toast) {
                    toast.classList.add('translate-y-20', 'opacity-0');
                    setTimeout(() => {
                        toast.remove();
                    }, 500);
                }
            }
        </script>
    @endif

    <!-- 1. GŁÓWNA KARTA ADMINA -->
    <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-xl border border-emerald-100 flex flex-col md:flex-row items-center md:items-start gap-8 mb-10 relative overflow-hidden">
        <div class="absolute -top-16 -right-16 w-64 h-64 bg-emerald-50 rounded-full opacity-50 blur-3xl z-0"></div>

        <div class="w-32 h-32 rounded-full border-4 border-emerald-500 overflow-hidden shadow-lg shrink-0 relative z-10">
            <img src="https://ui-avatars.com/api/?name=Admin+Admin&background=065f46&color=fff&size=256&bold=true" class="w-full h-full object-cover" alt="Avatar">
        </div>

        <div class="flex-grow text-center md:text-left relative z-10">
            <div class="inline-block bg-red-150 text-red-700 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest mb-3 italic"><i class="fa-solid fa-shield-halved mr-1 animate-pulse"></i> Panel Administratora</div>
            <h1 class="text-4xl font-black text-gray-900 italic uppercase tracking-tighter mb-1">{{ Auth::user()->name }}</h1>
            <p class="text-gray-500 font-bold italic mb-6 tracking-wide">{{ Auth::user()->email }}</p>

            <div class="flex flex-wrap justify-center md:justify-start gap-6">
                <div class="flex items-center text-emerald-700 font-black text-sm uppercase tracking-widest italic">
                    <i class="fa-solid fa-map-location-dot mr-2 text-emerald-500 text-lg"></i> {{ $spots->total() }} łowisk
                </div>
                <div class="flex items-center text-amber-600 font-black text-sm uppercase tracking-widest italic">
                    <i class="fa-solid fa-circle-exclamation mr-2 text-amber-500 text-lg"></i> {{ $pendingRecords->total() }} oczekujących rekordów
                </div>
                <div class="flex items-center text-gray-500 font-black text-sm uppercase tracking-widest italic">
                    <i class="fa-regular fa-comments mr-2 text-gray-400 text-lg"></i> {{ $reviews->total() }} opinii ogółem
                </div>
            </div>
        </div>

        <div class="relative z-10 mt-4 md:mt-0 flex gap-3">
            <button type="button" onclick="openAddSpotModal()" class="bg-emerald-700 hover:bg-emerald-800 text-white font-black px-6 py-3 rounded-2xl transition-all uppercase tracking-widest text-xs italic shadow-md hover:shadow-emerald-700/25 transform hover:-translate-y-0.5">
                <i class="fa-solid fa-plus mr-1.5"></i> Dodaj Łowisko
            </button>
        </div>
    </div>

    <!-- 2. ZAKŁADKI PANELU (TABS) Z JS -->
    <div class="flex border-b border-gray-200 mb-8 overflow-x-auto custom-scrollbar">
        <button onclick="switchAdminTab('lowiska')" id="btn-lowiska" class="px-6 py-4 text-emerald-700 border-b-4 border-emerald-700 font-black italic uppercase tracking-widest text-sm whitespace-nowrap transition-all">
            <i class="fa-solid fa-map-location-dot mr-2"></i> Zarządzaj Łowiskami ({{ $spots->total() }})
        </button>
        <button onclick="switchAdminTab('rekordy')" id="btn-rekordy" class="px-6 py-4 text-gray-400 hover:text-emerald-500 border-b-4 border-transparent font-bold italic uppercase tracking-widest text-sm whitespace-nowrap transition-all">
            <i class="fa-solid fa-circle-exclamation mr-2"></i> Weryfikacja rekordów ({{ $pendingRecords->total() }})
        </button>
        <button onclick="switchAdminTab('opinie')" id="btn-opinie" class="px-6 py-4 text-gray-400 hover:text-emerald-500 border-b-4 border-transparent font-bold italic uppercase tracking-widest text-sm whitespace-nowrap transition-all">
            <i class="fa-regular fa-comments mr-2"></i> Wszystkie opinie ({{ $reviews->total() }})
        </button>
    </div>

    <!-- ============================================== -->
    <!-- ZAKŁADKA 1: ZARZĄDZAJ ŁOWISKAMI -->
    <!-- ============================================== -->
    <div id="tab-lowiska" class="block animate-fadeIn">
        <!-- Wyszukiwarka -->
        <div class="mb-8 flex justify-between items-center flex-wrap gap-4">
            <div class="relative w-full md:w-[400px]">
                <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 transform -translate-y-1/2 text-emerald-500"></i>
                <input type="text" id="spot-search-input" placeholder="Szukaj łowiska (nazwa lub miasto)..." class="w-full bg-white border border-emerald-100 rounded-2xl py-4 pl-12 pr-4 text-sm font-bold text-gray-700 shadow-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 italic" id="spots-grid">
            @forelse($spots as $spot)
                @php
                    $avgRating = $spot->reviews->count() > 0 ? number_format($spot->reviews->avg('rating'), 1) : '0.0';
                    $slug = Str::slug($spot->name);
                @endphp
                <div onclick="window.location='{{ url('/lowiska/' . $spot->id . '-' . $slug) }}'" class="admin-spot-card group bg-white rounded-[2rem] shadow-sm hover:shadow-xl transition-all duration-500 overflow-hidden border border-gray-100 flex flex-col h-full relative cursor-pointer" data-name="{{ mb_strtolower($spot->name) }}" data-city="{{ mb_strtolower($spot->city) }}">
                    <!-- Zdjęcie łowiska -->
                    <div class="relative h-48 overflow-hidden">
                        <img src="{{ $spot->main_image_url }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-md px-4 py-1.5 rounded-full shadow-sm">
                            <span class="text-[10px] font-black text-gray-800 uppercase tracking-widest">{{ $spot->type }}</span>
                        </div>
                    </div>
                    <!-- Treść karty -->
                    <div class="p-6 flex flex-col flex-grow">
                        <h3 class="font-black text-xl text-gray-800 leading-tight group-hover:text-emerald-700 transition-colors mb-2 uppercase">{{ $spot->name }}</h3>
                        <p class="text-gray-500 text-xs font-bold flex items-center gap-1.5 mb-2 uppercase tracking-wider">
                            <i class="fa-solid fa-location-dot text-emerald-500 w-4"></i> {{ $spot->city }} ({{ $spot->region }})
                        </p>
                        <p class="text-gray-400 text-xs font-bold flex items-center gap-1.5 mb-4 uppercase tracking-wider">
                            <i class="fa-solid fa-fish text-gray-300 w-4"></i> {{ count($spot->fish_species ?? []) }} gatunków ryb
                        </p>
                        <div class="mt-auto pt-4 border-t border-gray-50 flex justify-between items-center">
                            <a href="/lowiska/{{ $spot->id }}-{{ $slug }}" onclick="event.stopPropagation()" class="text-emerald-600 hover:text-emerald-800 text-xs font-black uppercase tracking-wider italic flex items-center gap-1">
                                Podgląd <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                            <!-- CRUD Usuwanie -->
                            <form action="{{ route('admin.spots.destroy', $spot->id) }}" method="POST" class="admin-confirm-form inline" data-title="Usuwanie łowiska" data-text="Czy na pewno chcesz usunąć łowisko '{{ $spot->name }}' i wszystkie przypisane do niego rekordy oraz opinie?" data-confirm-text="Usuń łowisko" data-type="danger" onclick="event.stopPropagation()">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-10 h-10 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-800 transition flex items-center justify-center shadow-sm" title="Usuń łowisko">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-3xl p-10 text-center border border-gray-100">
                    <p class="text-gray-500 font-bold">Brak łowisk w bazie danych.</p>
                </div>
            @endforelse
        </div>
        @if($spots->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $spots->appends(request()->except('spots_page'))->links('partials.pagination') }}
            </div>
        @endif
    </div>

    <!-- ============================================== -->
    <!-- ZAKŁADKA 2: WERYFIKACJA REKORDÓW -->
    <!-- ============================================== -->
    <div id="tab-rekordy" class="hidden animate-fadeIn">
        <!-- Wyszukiwarka -->
        <div class="mb-8 flex justify-between items-center flex-wrap gap-4">
            <div class="relative w-full md:w-[400px]">
                <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 transform -translate-y-1/2 text-emerald-500"></i>
                <input type="text" id="record-search-input" placeholder="Szukaj (ryba, łowisko, użytkownik)..." class="w-full bg-white border border-emerald-100 rounded-2xl py-4 pl-12 pr-4 text-sm font-bold text-gray-700 shadow-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
            </div>
        </div>

        <div class="space-y-6 italic" id="records-grid">
            @forelse($pendingRecords as $record)
                @php
                    $spot = $record->fishing_spot;
                    $slug = $spot ? Str::slug($spot->name) : '';
                @endphp
                <div class="admin-record-card bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 flex flex-col md:flex-row items-center gap-6 group relative" data-fish="{{ mb_strtolower($record->fish_name) }}" data-spot="{{ mb_strtolower($spot->name ?? '') }}" data-user="{{ mb_strtolower($record->user->name ?? '') }}">
                    <!-- Zdjęcie okazu -->
                    <div class="w-32 h-32 rounded-[1.5rem] overflow-hidden shrink-0 shadow-md">
                        <img src="{{ $record->photo_url ?? 'https://images.unsplash.com/photo-1544983226-9d32b575eaee?w=800&q=80' }}" class="w-full h-full object-cover">
                    </div>
                    <!-- Dane rekordu -->
                    <div class="flex-grow text-center md:text-left">
                        <div class="inline-block bg-amber-100 text-amber-700 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-hourglass-half mr-1"></i> Oczekuje na zatwierdzenie</div>
                        <h3 class="text-3xl font-black uppercase text-gray-800 tracking-tight">{{ $record->fish_name }}</h3>
                        <p class="text-emerald-600 font-bold uppercase tracking-widest text-xs mt-1">
                            <i class="fa-solid fa-location-dot"></i> {{ $spot->name ?? 'Nieznane łowisko' }}
                        </p>
                        <p class="text-gray-500 font-black uppercase tracking-wide text-[10px] mt-1">
                            Zgłosił: <span class="text-emerald-700">{{ $record->user->name ?? 'Nieznany użytkownik' }}</span>
                        </p>
                    </div>
                    <!-- Waga i długość -->
                    <div class="flex gap-6 bg-gray-50 px-6 py-4 rounded-[1.5rem] border border-gray-100">
                        <div class="text-center">
                            <span class="block text-3xl font-black text-emerald-700 tracking-tighter">{{ $record->weight }} <span class="text-[10px] text-emerald-500 uppercase">kg</span></span>
                        </div>
                        <div class="w-px bg-gray-200"></div>
                        <div class="text-center">
                            <span class="block text-3xl font-black text-amber-500 tracking-tighter">{{ $record->length }} <span class="text-[10px] text-amber-400 uppercase">cm</span></span>
                        </div>
                    </div>
                    <!-- Decyzje Moderatora -->
                    <div class="flex md:flex-col gap-3 shrink-0">
                        <!-- Akceptuj -->
                        <form action="{{ route('admin.records.approve', $record->id) }}" method="POST" class="admin-confirm-form inline" data-title="Zatwierdzenie rekordu" data-text="Czy na pewno chcesz zatwierdzić ten rekord ryby ({{ $record->fish_name }}, {{ $record->weight }} kg) zgłoszony przez {{ $record->user->name ?? 'Wędkarza' }}?" data-confirm-text="Zatwierdź rekord" data-type="success">
                            @csrf
                            <button type="submit" class="w-12 h-12 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white transition flex items-center justify-center shadow-md hover:shadow-emerald-500/25 transform hover:-translate-y-0.5" title="Zatwierdź rekord">
                                <i class="fa-solid fa-check text-lg"></i>
                            </button>
                        </form>
                        <!-- Odrzuć -->
                        <form action="{{ route('admin.records.reject', $record->id) }}" method="POST" class="admin-confirm-form inline" data-title="Odrzucenie rekordu" data-text="Czy na pewno chcesz odrzucić i usunąć ten rekord ryby ({{ $record->fish_name }}, {{ $record->weight }} kg) zgłoszony przez {{ $record->user->name ?? 'Wędkarza' }}?" data-confirm-text="Odrzuć rekord" data-type="danger">
                            @csrf
                            <button type="submit" class="w-12 h-12 rounded-2xl bg-red-500 hover:bg-red-600 text-white transition flex items-center justify-center shadow-md hover:shadow-red-500/25 transform hover:-translate-y-0.5" title="Odrzuć rekord">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-3xl p-10 text-center border border-gray-100">
                    <p class="text-gray-500 font-bold">Wszystkie nadesłane rekordy zostały zweryfikowane!</p>
                </div>
            @endforelse
        </div>
        @if($pendingRecords->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $pendingRecords->appends(request()->except('records_page'))->links('partials.pagination') }}
            </div>
        @endif
    </div>

    <!-- ============================================== -->
    <!-- ZAKŁADKA 3: WSZYSTKIE OPINIE -->
    <!-- ============================================== -->
    <div id="tab-opinie" class="hidden animate-fadeIn">
        <!-- Wyszukiwarka -->
        <div class="mb-8 flex justify-between items-center flex-wrap gap-4">
            <div class="relative w-full md:w-[400px]">
                <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 transform -translate-y-1/2 text-emerald-500"></i>
                <input type="text" id="review-search-input" placeholder="Szukaj (łowisko, treść, użytkownik)..." class="w-full bg-white border border-emerald-100 rounded-2xl py-4 pl-12 pr-4 text-sm font-bold text-gray-700 shadow-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 italic" id="reviews-grid">
            @forelse($reviews as $review)
                @php
                    $spot = $review->fishing_spot;
                    $slug = $spot ? Str::slug($spot->name) : '';
                @endphp
                <div class="admin-review-card bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100 flex flex-col justify-between transform transition-all hover:shadow-xl relative group" data-spot="{{ mb_strtolower($spot->name ?? '') }}" data-user="{{ mb_strtolower($review->user->name ?? '') }}" data-comment="{{ mb_strtolower($review->comment ?? '') }}">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="font-black text-xl uppercase text-gray-800 tracking-tight">{{ $spot->name ?? 'Nieznane łowisko' }}</h4>
                                <p class="text-[10px] text-gray-400 font-bold mt-1 uppercase">Autor: <span class="text-emerald-700">{{ $review->user->name ?? 'Anonim' }}</span></p>
                                <div class="text-amber-400 text-xs mt-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <i class="fa-solid fa-star"></i>
                                        @elseif($i - 0.5 <= $review->rating)
                                            <i class="fa-solid fa-star-half-stroke"></i>
                                        @else
                                            <i class="fa-regular fa-star text-gray-200"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">{{ $review->created_at->format('Y-m-d') }}</span>
                        </div>
                        <p class="text-gray-500 font-medium leading-relaxed mb-6">{{ $review->comment }}</p>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-50 flex justify-between items-center mt-auto">
                        <a href="/lowiska/{{ $spot->id }}-{{ $slug }}#opinie" class="text-emerald-600 hover:text-emerald-800 text-xs font-black uppercase tracking-wider flex items-center gap-1">
                            Podgląd na łowisku <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                        <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="admin-confirm-form inline" data-title="Usuwanie opinii" data-text="Czy na pewno chcesz bezpowrotnie usunąć opinię wędkarza {{ $review->user->name ?? 'Wędkarz' }}?" data-confirm-text="Usuń opinię" data-type="danger">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-10 h-10 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-800 transition flex items-center justify-center shadow-sm" title="Usuń opinię">
                                <i class="fa-solid fa-trash-can text-sm"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-3xl p-10 text-center border border-gray-100">
                    <p class="text-gray-500 font-bold">Brak opinii o łowiskach.</p>
                </div>
            @endforelse
        </div>
        @if($reviews->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $reviews->appends(request()->except('reviews_page'))->links('partials.pagination') }}
            </div>
        @endif
    </div>

    <!-- ============================================== -->
    <!-- MODAL: DODAJ NOWE ŁOWISKO (CRUD) -->
    <!-- ============================================== -->
    <div id="add-spot-modal" class="fixed inset-0 z-[9999] hidden opacity-0 transition-all duration-300 flex items-center justify-center p-4">
        <!-- Zamazane tło -->
        <div class="absolute inset-0 bg-emerald-950/70 backdrop-blur-md cursor-pointer" onclick="closeAddSpotModal()"></div>

        <!-- Karta Modala -->
        <div class="relative z-10 w-full max-w-[600px] transform scale-95 transition-all duration-300 flex flex-col" id="add-spot-card">
            <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden w-full max-h-[90vh] flex flex-col">

                <!-- Nagłówek Modala -->
                <div class="bg-emerald-900 pt-8 pb-6 text-center relative overflow-hidden shrink-0">
                    <div class="absolute inset-0 z-0">
                        <img src="https://images.unsplash.com/photo-1506125840744-167167210587?q=80&w=1200" class="w-full h-full object-cover blur-[3px] opacity-40 scale-110">
                    </div>
                    <div class="relative z-10">
                        <h2 class="text-2xl font-black text-white tracking-tight italic uppercase drop-shadow-md">Dodaj Nowe Łowisko</h2>
                        <p class="text-emerald-200 text-[10px] font-bold opacity-90 mt-1 uppercase tracking-widest">Wprowadź parametry łowiska do bazy</p>
                    </div>
                </div>

                <!-- Formularz w scrollowalnej sekcji -->
                <div class="p-8 md:p-10 overflow-y-auto custom-scrollbar flex-grow">
                    
                    @if($errors->any())
                        <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-800 font-bold rounded-2xl flex flex-col gap-1.5 shadow-sm text-sm">
                            <div class="flex items-center gap-2 text-red-955 font-black uppercase text-xs tracking-wider">
                                <i class="fa-solid fa-circle-exclamation text-base text-red-650"></i>
                                <span>Błędy walidacji</span>
                            </div>
                            <ul class="list-disc list-inside text-xs space-y-0.5 font-bold">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="form-add-spot" method="POST" action="{{ route('admin.spots.store') }}" enctype="multipart/form-data" class="space-y-5 italic">
                        @csrf

                        <!-- Nazwa łowiska -->
                        <div>
                            <label class="block text-emerald-800 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-heading mr-1"></i> Nazwa Łowiska *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-emerald-50/50 focus:bg-white border-2 border-emerald-100 focus:border-emerald-500 rounded-2xl px-5 py-3 text-gray-800 font-bold placeholder-gray-400 focus:outline-none transition-all" placeholder="np. Jezioro Śniardwy">
                        </div>

                        <!-- Typ wody -->
                        <div>
                            <label class="block text-emerald-800 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-water mr-1"></i> Typ wody *</label>
                            <select name="type" required class="w-full bg-emerald-50/50 focus:bg-white border-2 border-emerald-100 focus:border-emerald-500 rounded-2xl px-5 py-3 text-gray-800 font-bold focus:outline-none transition-all">
                                <option value="jezioro" {{ old('type') == 'jezioro' ? 'selected' : '' }}>Jezioro</option>
                                <option value="rzeka" {{ old('type') == 'rzeka' ? 'selected' : '' }}>Rzeka</option>
                                <option value="zalew" {{ old('type') == 'zalew' ? 'selected' : '' }}>Zalew</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Miejscowość -->
                            <div>
                                <label class="block text-emerald-800 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-city mr-1"></i> Miejscowość *</label>
                                <input type="text" name="city" value="{{ old('city') }}" required class="w-full bg-emerald-50/50 focus:bg-white border-2 border-emerald-100 focus:border-emerald-500 rounded-2xl px-5 py-3 text-gray-800 font-bold placeholder-gray-400 focus:outline-none transition-all" placeholder="np. Mikołajki">
                            </div>

                            <!-- Województwo -->
                            <div>
                                <label class="block text-emerald-800 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-map mr-1"></i> Województwo *</label>
                                <input type="text" name="region" value="{{ old('region') }}" required class="w-full bg-emerald-50/50 focus:bg-white border-2 border-emerald-100 focus:border-emerald-500 rounded-2xl px-5 py-3 text-gray-800 font-bold placeholder-gray-400 focus:outline-none transition-all" placeholder="np. Warmińsko-Mazurskie">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Szerokość geo (lat) -->
                            <div>
                                <label class="block text-emerald-800 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-location-crosshairs mr-1"></i> Szerokość (Lat) *</label>
                                <input type="number" step="any" name="lat" value="{{ old('lat') }}" required class="w-full bg-emerald-50/50 focus:bg-white border-2 border-emerald-100 focus:border-emerald-500 rounded-2xl px-5 py-3 text-gray-800 font-bold placeholder-gray-400 focus:outline-none transition-all" placeholder="np. 53.7845">
                            </div>

                            <!-- Długość geo (lng) -->
                            <div>
                                <label class="block text-emerald-800 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-location-crosshairs mr-1"></i> Długość (Lng) *</label>
                                <input type="number" step="any" name="lng" value="{{ old('lng') }}" required class="w-full bg-emerald-50/50 focus:bg-white border-2 border-emerald-100 focus:border-emerald-500 rounded-2xl px-5 py-3 text-gray-800 font-bold placeholder-gray-400 focus:outline-none transition-all" placeholder="np. 21.5712">
                            </div>
                        </div>

                        <!-- Opis łowiska -->
                        <div>
                            <label class="block text-emerald-800 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-quote-left mr-1"></i> Opis Łowiska</label>
                            <textarea name="description" rows="3" class="w-full bg-emerald-50/50 focus:bg-white border-2 border-emerald-100 focus:border-emerald-500 rounded-2xl px-5 py-3 text-gray-800 font-bold placeholder-gray-400 focus:outline-none transition-all" placeholder="Krótka charakterystyka łowiska...">{{ old('description') }}</textarea>
                        </div>

                        <!-- Zdjęcie łowiska -->
                        <div>
                            <label class="block text-emerald-800 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-image mr-1"></i> Zdjęcie Łowiska *</label>
                            <div class="flex justify-center px-6 pt-5 pb-5 border-2 border-emerald-100 border-dashed rounded-2xl hover:border-emerald-500 transition-colors cursor-pointer bg-emerald-50/30" onclick="document.getElementById('spot-image-upload').click()">
                                <div class="space-y-1 text-center">
                                    <i class="fa-solid fa-cloud-arrow-up text-2xl text-emerald-600"></i>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <span id="spot-image-text" class="font-bold text-emerald-700 hover:text-emerald-800">Kliknij, aby wybrać plik</span>
                                        <input id="spot-image-upload" name="main_image" type="file" required class="sr-only" accept="image/*" onchange="document.getElementById('spot-image-text').innerText = this.files[0].name; document.getElementById('spot-image-text').className = 'font-bold text-gray-800'">
                                    </div>
                                    <p class="text-[9px] text-gray-400 font-medium uppercase tracking-wide">Maks. 5MB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Udogodnienia -->
                        <div>
                            <label class="block text-emerald-800 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-circle-check mr-1"></i> Udogodnienia *</label>
                            <div class="grid grid-cols-2 gap-3 bg-emerald-50/30 p-4 rounded-2xl border border-emerald-100 text-left font-bold">
                                @foreach($allAmenities as $amenity)
                                    @php
                                        $oldAmenities = old('amenities');
                                        $hasAmenity = is_array($oldAmenities) && in_array($amenity->id, $oldAmenities);
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
                            <label class="block text-emerald-800 text-xs font-black uppercase tracking-widest mb-2"><i class="fa-solid fa-fish mr-1"></i> Struktura zarybienia (ryby i %)*</label>
                            <div class="bg-emerald-50/30 p-4 rounded-2xl border border-emerald-100 max-h-60 overflow-y-auto custom-scrollbar space-y-2 text-left font-bold">
                                @foreach($allSpecies as $species)
                                    @php
                                        $oldCheck = old('fish_species_check');
                                        $isChecked = is_array($oldCheck) && in_array($species->id, $oldCheck);
                                        $oldPct = old('fish_species_pct.' . $species->id);
                                        $pct = !is_null($oldPct) ? (int)$oldPct : 0;
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
                            <button type="button" onclick="closeAddSpotModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-600 font-black px-6 py-4 rounded-2xl transition-all uppercase tracking-widest text-xs">
                                Anuluj
                            </button>
                            <button type="submit" class="flex-1 bg-emerald-700 hover:bg-emerald-800 text-white font-black px-6 py-4 rounded-2xl transition-all uppercase tracking-widest text-xs shadow-md">
                                Zapisz Łowisko
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</main>

<script>
    // Walidacja struktury zarybienia po stronie klienta
    window.addEventListener('DOMContentLoaded', () => {
        const addForm = document.getElementById('form-add-spot');
        if (addForm) {
            addForm.addEventListener('submit', function(e) {
                const checkedBoxes = addForm.querySelectorAll('input[name="fish_species_check[]"]:checked');
                let totalPct = 0;
                checkedBoxes.forEach(cb => {
                    const pctInput = addForm.querySelector(`input[name="fish_species_pct[${cb.value}]"]`);
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

        // Automatyczne włączenie odpowiedniej zakładki przy paginacji
        const urlParams = new URLSearchParams(window.location.search);
        let tab = 'lowiska';
        if (urlParams.has('records_page')) {
            tab = 'rekordy';
        } else if (urlParams.has('reviews_page')) {
            tab = 'opinie';
        } else if (urlParams.has('spots_page')) {
            tab = 'lowiska';
        }
        switchAdminTab(tab);

        // Wyszukiwarka łowisk
        const searchInput = document.getElementById('spot-search-input');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase();
                const spotCards = document.querySelectorAll('.admin-spot-card');
                let visibleCount = 0;
                
                spotCards.forEach(card => {
                    const name = card.getAttribute('data-name') || '';
                    const city = card.getAttribute('data-city') || '';
                    
                    if (name.includes(term) || city.includes(term)) {
                        card.style.display = 'flex';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                const emptyState = document.getElementById('spots-empty-search');
                if (visibleCount === 0 && spotCards.length > 0) {
                    if (!emptyState) {
                        const emptyDiv = document.createElement('div');
                        emptyDiv.id = 'spots-empty-search';
                        emptyDiv.className = 'col-span-full bg-white rounded-3xl p-10 text-center border border-gray-100';
                        emptyDiv.innerHTML = '<p class="text-gray-500 font-bold">Brak łowisk pasujących do wyszukiwania.</p>';
                        document.getElementById('spots-grid').appendChild(emptyDiv);
                    }
                } else if (emptyState) {
                    emptyState.remove();
                }
            });
        }

        // Wyszukiwarka rekordów
        const recordSearchInput = document.getElementById('record-search-input');
        if (recordSearchInput) {
            recordSearchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase();
                const recordCards = document.querySelectorAll('.admin-record-card');
                let visibleCount = 0;
                
                recordCards.forEach(card => {
                    const fish = card.getAttribute('data-fish') || '';
                    const spot = card.getAttribute('data-spot') || '';
                    const user = card.getAttribute('data-user') || '';
                    
                    if (fish.includes(term) || spot.includes(term) || user.includes(term)) {
                        card.style.display = 'flex';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                const emptyState = document.getElementById('records-empty-search');
                if (visibleCount === 0 && recordCards.length > 0) {
                    if (!emptyState) {
                        const emptyDiv = document.createElement('div');
                        emptyDiv.id = 'records-empty-search';
                        emptyDiv.className = 'bg-white rounded-3xl p-10 text-center border border-gray-100';
                        emptyDiv.innerHTML = '<p class="text-gray-500 font-bold">Brak rekordów pasujących do wyszukiwania.</p>';
                        document.getElementById('records-grid').appendChild(emptyDiv);
                    }
                } else if (emptyState) {
                    emptyState.remove();
                }
            });
        }

        // Wyszukiwarka opinii
        const reviewSearchInput = document.getElementById('review-search-input');
        if (reviewSearchInput) {
            reviewSearchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase();
                const reviewCards = document.querySelectorAll('.admin-review-card');
                let visibleCount = 0;
                
                reviewCards.forEach(card => {
                    const spot = card.getAttribute('data-spot') || '';
                    const user = card.getAttribute('data-user') || '';
                    const comment = card.getAttribute('data-comment') || '';
                    
                    if (spot.includes(term) || user.includes(term) || comment.includes(term)) {
                        card.style.display = 'flex';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                const emptyState = document.getElementById('reviews-empty-search');
                if (visibleCount === 0 && reviewCards.length > 0) {
                    if (!emptyState) {
                        const emptyDiv = document.createElement('div');
                        emptyDiv.id = 'reviews-empty-search';
                        emptyDiv.className = 'col-span-full bg-white rounded-3xl p-10 text-center border border-gray-100';
                        emptyDiv.innerHTML = '<p class="text-gray-500 font-bold">Brak opinii pasujących do wyszukiwania.</p>';
                        document.getElementById('reviews-grid').appendChild(emptyDiv);
                    }
                } else if (emptyState) {
                    emptyState.remove();
                }
            });
        }
    });

    // Obsługa zakładek (Tabs) panelu administratora
    function switchAdminTab(tabName) {
        // Wszystkie kontenery tabów
        const tabLowiska = document.getElementById('tab-lowiska');
        const tabRekordy = document.getElementById('tab-rekordy');
        const tabOpinie = document.getElementById('tab-opinie');

        // Wszystkie buttony
        const btnLowiska = document.getElementById('btn-lowiska');
        const btnRekordy = document.getElementById('btn-rekordy');
        const btnOpinie = document.getElementById('btn-opinie');

        // Klasy aktywne i nieaktywne
        const activeBtnClasses = ['text-emerald-700', 'border-emerald-700', 'font-black'];
        const inactiveBtnClasses = ['text-gray-400', 'hover:text-emerald-500', 'border-transparent', 'font-bold'];

        // Ukryj wszystkie taby
        tabLowiska.classList.replace('block', 'hidden');
        tabRekordy.classList.replace('block', 'hidden');
        tabOpinie.classList.replace('block', 'hidden');

        // Przywróć nieaktywne style do wszystkich przycisków
        [btnLowiska, btnRekordy, btnOpinie].forEach(btn => {
            btn.classList.remove(...activeBtnClasses);
            btn.classList.add(...inactiveBtnClasses);
        });

        // Pokaż wybrany tab i ustaw aktywny przycisk
        if (tabName === 'lowiska') {
            tabLowiska.classList.replace('hidden', 'block');
            btnLowiska.classList.remove(...inactiveBtnClasses);
            btnLowiska.classList.add(...activeBtnClasses);
        } else if (tabName === 'rekordy') {
            tabRekordy.classList.replace('hidden', 'block');
            btnRekordy.classList.remove(...inactiveBtnClasses);
            btnRekordy.classList.add(...activeBtnClasses);
        } else if (tabName === 'opinie') {
            tabOpinie.classList.replace('hidden', 'block');
            btnOpinie.classList.remove(...inactiveBtnClasses);
            btnOpinie.classList.add(...activeBtnClasses);
        }
    }

    // Modal dodawania nowego łowiska
    function openAddSpotModal() {
        const modal = document.getElementById('add-spot-modal');
        const card = document.getElementById('add-spot-card');

        modal.classList.remove('hidden');
        // Wymuszenie reflow dla animacji
        modal.offsetHeight; 
        modal.classList.add('opacity-100');
        card.classList.remove('scale-95');
        card.classList.add('scale-100');
        document.body.style.overflow = 'hidden';
    }

    function closeAddSpotModal() {
        const modal = document.getElementById('add-spot-modal');
        const card = document.getElementById('add-spot-card');

        modal.classList.remove('opacity-100');
        card.classList.remove('scale-100');
        card.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    }

    @if($errors->any())
        window.addEventListener('load', () => {
            setTimeout(openAddSpotModal, 500);
        });
    @endif

    // Custom Confirmation Modal Logic
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

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>
@endsection
