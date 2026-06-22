@extends('layouts.fishing')

@section('title', 'Mój Profil - Wędkarski Radar')

@section('content')
<main class="container mx-auto px-4 py-10 flex-grow max-w-5xl">

    <!-- 1. GŁÓWNA KARTA PROFILU -->
    <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-xl border border-emerald-100 flex flex-col md:flex-row items-center md:items-start gap-8 mb-10 relative overflow-hidden">
        <div class="absolute -top-16 -right-16 w-64 h-64 bg-emerald-50 rounded-full opacity-50 blur-3xl z-0"></div>

        <div class="w-32 h-32 rounded-full border-4 border-emerald-500 overflow-hidden shadow-lg shrink-0 relative z-10">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Jan') }}&background=065f46&color=fff&size=256&bold=true" class="w-full h-full object-cover" alt="Avatar">
        </div>

        <div class="flex-grow text-center md:text-left relative z-10">
            <h1 class="text-4xl font-black text-gray-900 italic uppercase tracking-tighter mb-1">{{ Auth::user()->name ?? 'Jan Kowalski' }}</h1>
            <p class="text-gray-500 font-bold italic mb-6 tracking-wide">{{ Auth::user()->email ?? 'jan@lowiska.pl' }}</p>

            <div class="flex flex-wrap justify-center md:justify-start gap-6">
                <div class="flex items-center text-emerald-700 font-black text-sm uppercase tracking-widest italic">
                    <i class="fa-solid fa-heart mr-2 text-emerald-500 text-lg"></i> {{ $favoriteSpots->total() }} ulubione
                </div>
                <div class="flex items-center text-amber-600 font-black text-sm uppercase tracking-widest italic">
                    <i class="fa-solid fa-trophy mr-2 text-amber-500 text-lg"></i> {{ $records->total() }} rekordy
                </div>
                <div class="flex items-center text-gray-500 font-black text-sm uppercase tracking-widest italic">
                    <i class="fa-regular fa-comments mr-2 text-gray-400 text-lg"></i> {{ $reviews->total() }} opinie
                </div>
            </div>
        </div>

        <div class="relative z-10 mt-4 md:mt-0">
            <button type="button" onclick="openEditProfileModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-black px-6 py-3 rounded-2xl transition-all uppercase tracking-widest text-xs italic shadow-sm">
                Edytuj profil
            </button>
        </div>
    </div>

    <!-- 2. ZAKŁADKI (TABS) Z JS -->
    <div class="flex border-b border-gray-200 mb-8 overflow-x-auto custom-scrollbar">
        <button onclick="switchProfileTab('ulubione')" id="btn-ulubione" class="px-6 py-4 text-emerald-700 border-b-4 border-emerald-700 font-black italic uppercase tracking-widest text-sm whitespace-nowrap transition-all">
            <i class="fa-solid fa-heart mr-2"></i> Ulubione łowiska ({{ $favoriteSpots->total() }})
        </button>
        <button onclick="switchProfileTab('rekordy')" id="btn-rekordy" class="px-6 py-4 text-gray-400 hover:text-emerald-500 border-b-4 border-transparent font-bold italic uppercase tracking-widest text-sm whitespace-nowrap transition-all">
            <i class="fa-solid fa-trophy mr-2"></i> Ranking rekordów ({{ $records->total() }})
        </button>
        <button onclick="switchProfileTab('opinie')" id="btn-opinie" class="px-6 py-4 text-gray-400 hover:text-emerald-500 border-b-4 border-transparent font-bold italic uppercase tracking-widest text-sm whitespace-nowrap transition-all">
            <i class="fa-regular fa-comments mr-2"></i> Opinie ({{ $reviews->total() }})
        </button>
    </div>

    <!-- ============================================== -->
    <!-- ZAKŁADKA 1: ULUBIONE ŁOWISKA -->
    <!-- ============================================== -->
    <div id="tab-ulubione" class="block">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 italic">
            @forelse($favoriteSpots as $spot)
                @php
                    $avgRating = $spot->reviews->count() > 0 ? number_format($spot->reviews->avg('rating'), 1) : '0.0';
                    $slug = Str::slug($spot->name);
                @endphp
                <a href="/lowiska/{{ $spot->id }}-{{ $slug }}" class="group bg-white rounded-[2rem] shadow-sm hover:shadow-xl transition-all duration-500 overflow-hidden border border-gray-100 flex flex-col h-full transform hover:-translate-y-1">
                    <div class="relative h-48 overflow-hidden">
                        <img src="{{ $spot->main_image_url }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-md px-4 py-1.5 rounded-full shadow-sm">
                            <span class="text-[10px] font-black text-gray-800 uppercase tracking-widest">{{ $spot->type }}</span>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <h3 class="font-black text-xl text-gray-800 leading-tight group-hover:text-emerald-700 transition-colors mb-2 uppercase">{{ $spot->name }}</h3>
                        <p class="text-gray-500 text-xs font-bold flex items-center gap-1.5 mb-2 uppercase tracking-wider">
                            <i class="fa-solid fa-location-dot text-emerald-500 w-4"></i> {{ $spot->city }}
                        </p>
                        <p class="text-gray-400 text-xs font-bold flex items-center gap-1.5 mb-4 uppercase tracking-wider">
                            <i class="fa-solid fa-fish text-gray-300 w-4"></i> {{ count($spot->fish_species) }} gatunków ryb
                        </p>
                        <div class="mt-auto pt-4 border-t border-gray-50 flex justify-between items-center">
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-star {{ $spot->reviews->count() > 0 ? 'text-amber-400' : 'text-gray-300' }} text-sm"></i>
                                <span class="text-sm font-black text-gray-800">{{ $avgRating }}</span>
                            </div>
                            <button type="button" onclick="event.preventDefault(); removeFavorite(this, {{ $spot->id }});" class="text-red-500 hover:text-gray-400 transition text-lg focus:outline-none animate-pulse-slow" title="Usuń z ulubionych">
                                <i class="fa-solid fa-heart transition-all duration-300 transform hover:scale-110"></i>
                            </button>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full bg-white rounded-3xl p-10 text-center border border-gray-100">
                    <p class="text-gray-500 font-bold">Brak ulubionych łowisk.</p>
                </div>
            @endforelse
        </div>
        @if($favoriteSpots->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $favoriteSpots->appends(request()->except('spots_page'))->links('partials.pagination') }}
            </div>
        @endif
    </div>

    <!-- ============================================== -->
    <!-- ZAKŁADKA 2: RANKING REKORDÓW -->
    <!-- ============================================== -->
    <div id="tab-rekordy" class="hidden">
        <div class="space-y-6 italic">
            @forelse($records as $record)
                @php
                    $spot = $record->fishing_spot;
                    $slug = $spot ? Str::slug($spot->name) : '';
                @endphp
                <a href="{{ $spot ? '/lowiska/'.$spot->id.'-'.$slug.'#rekordy' : '#' }}" class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 flex flex-col md:flex-row items-center gap-6 transform transition-all hover:-translate-y-1 hover:shadow-xl group">
                    <div class="w-32 h-32 rounded-[1.5rem] overflow-hidden shrink-0 shadow-md">
                        <img src="{{ $record->photo_url ?? 'https://images.unsplash.com/photo-1544983226-9d32b575eaee?w=800&q=80' }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    </div>
                    <div class="flex-grow text-center md:text-left">
                        @if($record->status === 'pending')
                            <div class="inline-block bg-amber-100 text-amber-700 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest mb-2 italic">
                                <i class="fa-solid fa-clock mr-1 animate-pulse"></i> Oczekuje na weryfikację
                            </div>
                        @elseif($record->status === 'rejected')
                            <div class="inline-block bg-red-100 text-red-700 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest mb-2 italic">
                                <i class="fa-solid fa-circle-xmark mr-1"></i> Odrzucono
                            </div>
                        @else
                            <div class="inline-block bg-emerald-100 text-emerald-700 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest mb-2">
                                <i class="fa-solid fa-trophy mr-1"></i> {{ $record->getRank() }}. miejsce w rankingu
                            </div>
                        @endif
                        <h3 class="text-3xl font-black uppercase text-gray-800 tracking-tight">{{ $record->fish_name }}</h3>
                        <p class="text-emerald-600 font-bold uppercase tracking-widest text-xs mt-1"><i class="fa-solid fa-location-dot"></i> {{ $spot->name ?? 'Nieznane łowisko' }}</p>
                        <p class="text-gray-400 font-medium text-[11px] mt-2 uppercase tracking-wide">Złowiono: {{ \Carbon\Carbon::parse($record->caught_at)->format('Y-m-d') }}</p>
                    </div>
                    <div class="flex gap-6 mt-4 md:mt-0 bg-gray-50 px-6 py-4 rounded-[1.5rem] border border-gray-100">
                        <div class="text-center">
                            <span class="block text-3xl font-black text-emerald-700 tracking-tighter">{{ $record->weight }} <span class="text-[10px] text-emerald-500 uppercase">kg</span></span>
                        </div>
                        <div class="w-px bg-gray-200"></div>
                        <div class="text-center">
                            <span class="block text-3xl font-black text-amber-500 tracking-tighter">{{ $record->length }} <span class="text-[10px] text-amber-400 uppercase">cm</span></span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-white rounded-3xl p-10 text-center border border-gray-100">
                    <p class="text-gray-500 font-bold">Brak zgłoszonych rekordów.</p>
                </div>
            @endforelse
        </div>
        @if($records->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $records->appends(request()->except('records_page'))->links('partials.pagination') }}
            </div>
        @endif
    </div>

    <!-- ============================================== -->
    <!-- ZAKŁADKA 3: OPINIE -->
    <!-- ============================================== -->
    <div id="tab-opinie" class="hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 italic">
            @forelse($reviews as $review)
                @php
                    $spot = $review->fishing_spot;
                    $slug = $spot ? Str::slug($spot->name) : '';
                @endphp
                <a href="{{ $spot ? '/lowiska/'.$spot->id.'-'.$slug.'#opinie' : '#' }}" class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100 transform transition-all hover:-translate-y-1 hover:shadow-xl block">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="font-black text-xl uppercase text-gray-800 tracking-tight hover:text-emerald-700 transition-colors">{{ $spot->name ?? 'Nieznane łowisko' }}</h4>
                            <div class="text-amber-400 text-xs mt-1">
                                @for($i=1; $i<=5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= $review->rating ? '' : 'text-gray-200' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">{{ $review->created_at->format('Y-m-d') }}</span>
                    </div>
                    <p class="text-gray-500 font-medium leading-relaxed">{{ $review->comment }}</p>
                </a>
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

    <div id="edit-profile-modal" class="fixed inset-0 z-[9999] hidden opacity-0 transition-all duration-300 flex items-center justify-center p-4">
        <!-- Zamazane tło -->
        <div class="absolute inset-0 bg-emerald-950/70 backdrop-blur-md cursor-pointer" onclick="closeEditProfileModal()"></div>

        <!-- Karta Modala -->
        <div class="relative z-10 w-full max-w-[500px] transform scale-95 transition-all duration-300 flex flex-col items-center" id="edit-profile-card">
            <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden w-full">

                <!-- Nagłówek Modala (Styl z Logowania) -->
                <div class="bg-emerald-900 pt-8 pb-6 text-center relative overflow-hidden">
                    <div class="absolute inset-0 z-0">
                        <img src="https://images.unsplash.com/photo-1518314115147-36b132808069?q=80&w=2000" class="w-full h-full object-cover blur-[3px] opacity-40 scale-110">
                    </div>
                    <div class="relative z-10">
                        <h2 class="text-2xl font-black text-white tracking-tight italic uppercase drop-shadow-md">Edytuj Profil</h2>
                        <p class="text-emerald-200 text-[10px] font-bold opacity-90 mt-1 uppercase tracking-widest">Dostosuj swoje dane</p>
                    </div>
                </div>

                <!-- Formularz (Przygotowany pod CRUD) -->
                <div class="p-8 md:p-10">
                    <form id="form-edit-profile" method="POST" action="#" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT') <!-- Metoda PUT jest używana w CRUD do edycji danych -->

                        <!-- Edycja Zdjęcia (Awatar) -->
                        <div class="flex flex-col items-center justify-center mb-6">
                            <div class="relative group cursor-pointer rounded-full">
                                <!-- Obecne zdjęcie -->
                                <div class="w-28 h-28 rounded-full border-4 border-emerald-500 overflow-hidden shadow-md">
                                    <img id="avatar-preview" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Jan') }}&background=065f46&color=fff&size=256&bold=true" class="w-full h-full object-cover transition-all">
                                </div>
                                <!-- Nakładka po najechaniu myszką -->
                                <label for="avatar-upload" class="absolute inset-0 bg-black/60 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer backdrop-blur-sm">
                                    <i class="fa-solid fa-camera text-white text-2xl"></i>
                                </label>
                                <!-- Niewidoczne pole pliku -->
                                <input type="file" id="avatar-upload" name="avatar" class="hidden" accept="image/*" onchange="previewAvatar(event)">
                            </div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-3">Kliknij zdjęcie, aby zmienić</span>
                        </div>

                        <!-- Edycja Imienia i Nazwiska -->
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 text-left">Imię i Nazwisko</label>
                            <div class="relative">
                                <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
                                <input type="text" name="name" value="{{ Auth::user()->name ?? '' }}" required placeholder="np. Jan Kowalski"
                                    class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border-2 border-emerald-500/20 rounded-2xl focus:border-emerald-500 focus:ring-0 outline-none transition-all text-sm font-bold text-gray-800">
                            </div>
                        </div>

                        <!-- Przyciski akcji -->
                        <div class="flex gap-4 pt-4">
                            <button type="button" onclick="closeEditProfileModal()" class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-black py-4 rounded-2xl transition-all uppercase tracking-widest text-xs italic shadow-sm">
                                Anuluj
                            </button>
                            <button type="submit" class="w-1/2 bg-amber-500 hover:bg-amber-600 text-white font-black py-4 rounded-2xl shadow-xl transition-all transform hover:-translate-y-1 flex items-center justify-center gap-2 tracking-widest text-xs uppercase italic">
                                <i class="fa-solid fa-floppy-disk"></i> Zapisz
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

</main>
@endsection

@section('scripts')
<script>
    function switchProfileTab(tabName) {
        // 1. Ukryj wszystkie sekcje
        document.getElementById('tab-ulubione').classList.add('hidden');
        document.getElementById('tab-rekordy').classList.add('hidden');
        document.getElementById('tab-opinie').classList.add('hidden');
        document.getElementById('tab-ulubione').classList.remove('block');
        document.getElementById('tab-rekordy').classList.remove('block');
        document.getElementById('tab-opinie').classList.remove('block');

        // 2. Zresetuj style wszystkich przycisków
        const btns = ['btn-ulubione', 'btn-rekordy', 'btn-opinie'];
        btns.forEach(id => {
            const btn = document.getElementById(id);
            btn.classList.remove('text-emerald-700', 'border-emerald-700', 'font-black');
            btn.classList.add('text-gray-400', 'border-transparent', 'font-bold');
        });

        // 3. Pokaż aktywną sekcję
        document.getElementById('tab-' + tabName).classList.remove('hidden');
        document.getElementById('tab-' + tabName).classList.add('block');

        // 4. Podświetl kliknięty przycisk
        const activeBtn = document.getElementById('btn-' + tabName);
        activeBtn.classList.remove('text-gray-400', 'border-transparent', 'font-bold');
        activeBtn.classList.add('text-emerald-700', 'border-emerald-700', 'font-black');
    }

    // Otwieranie Modala
    function openEditProfileModal() {
        const modal = document.getElementById('edit-profile-modal');
        const card = document.getElementById('edit-profile-card');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            card.classList.replace('scale-95', 'scale-100');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    // Zamykanie Modala
    function closeEditProfileModal() {
        const modal = document.getElementById('edit-profile-modal');
        const card = document.getElementById('edit-profile-card');
        modal.classList.add('opacity-0');
        card.classList.replace('scale-100', 'scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 300);
    }

    // Podgląd wybranego zdjęcia na żywo
    function previewAvatar(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Zaawansowana obsługa asynchronicznego usuwania łowiska z ulubionych bezpośrednio na profilu.
    // Skrypt wykonuje żądanie sieciowe, a następnie wyzwala płynną animację zanikania karty,
    // aktualizuje liczniki telemetryczne na profilu za pomocą wyrażeń regularnych (RegExp)
    // oraz w razie potrzeby wstrzykuje do DOM szablon pustego stanu – wszystko bez przeładowywania strony.
    function removeFavorite(btn, spotId) {
        // Ponieważ cała karta łowiska jest zawarta w tagu <a>, odnajdujemy najbliższego przodka
        const card = btn.closest('a');

        fetch(`/lowiska/${spotId}/toggle-favorite`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && !data.is_favorite) {
                // Krok 1: Wyzwolenie płynnej animacji zanikania w CSS.
                // Używamy bezierowskiego przejścia (cubic-bezier) dla maksymalnego efektu premium.
                // Karta zmniejsza skalę, przesuwa się lekko w dół i staje się przezroczysta w ciągu 400ms.
                card.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.9) translateY(10px)';
                
                // Krok 2: Odczekanie na zakończenie animacji (400ms) przed manipulacją strukturą DOM.
                setTimeout(() => {
                    card.remove();
                    
                    // Krok 3: Dynamiczna aktualizacja liczników na profilu (przycisk zakładki i widget statystyk).
                    // Wyszukujemy elementy na stronie zawierające aktualne liczby.
                    const counts = document.querySelectorAll('[id^="btn-ulubione"], .flex.items-center.text-emerald-700');
                    counts.forEach(el => {
                        let text = el.textContent;
                        // Wyrażenie regularne \d+ wyszukuje pierwszą liczbę w tekście elementu
                        let match = text.match(/\d+/);
                        if (match) {
                            let newCount = Math.max(0, parseInt(match[0]) - 1); // Dekrementacja o 1, blokując ujemne wartości
                            if (el.id === 'btn-ulubione') {
                                el.innerHTML = `<i class="fa-solid fa-heart mr-2"></i> Ulubione łowiska (${newCount})`;
                            } else {
                                el.innerHTML = `<i class="fa-solid fa-heart mr-2 text-emerald-500 text-lg"></i> ${newCount} ulubione`;
                            }
                        }
                    });
                    
                    // Krok 4: Sprawdzenie stanu tablicy ulubionych.
                    // Jeśli usunięte łowisko było ostatnim na liście, dynamicznie wstrzykujemy szablon pustego stanu.
                    const container = document.querySelector('#tab-ulubione > div.grid');
                    if (container && container.querySelectorAll('a').length === 0) {
                         container.outerHTML = `
                            <div class="col-span-full bg-white rounded-3xl p-10 text-center border border-gray-100 italic">
                                <p class="text-gray-500 font-bold">Brak ulubionych łowisk.</p>
                            </div>
                        `;
                    }
                }, 400);
            }
        })
        .catch(error => {
            console.error('Błąd podczas usuwania z ulubionych:', error);
        });
    }

    // Automatyczne włączenie odpowiedniej zakładki przy paginacji
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        let tab = 'ulubione';
        if (urlParams.has('records_page')) {
            tab = 'rekordy';
        } else if (urlParams.has('reviews_page')) {
            tab = 'opinie';
        } else if (urlParams.has('spots_page')) {
            tab = 'ulubione';
        }
        switchProfileTab(tab);
    });

</script>
@endsection
