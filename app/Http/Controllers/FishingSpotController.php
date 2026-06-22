<?php

namespace App\Http\Controllers;

use App\Models\FishingSpot;
use App\Models\FishSpecies;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

/**
 * Kontroler łowisk.
 * Odpowiada za wyświetlanie listy łowisk z filtrowaniem, strony szczegółowej,
 * mapy interaktywnej, zgłaszanie nowych rekordów ryb
 * oraz obsługę dodawania łowisk do ulubionych.
 */
class FishingSpotController extends Controller
{
    /**
     * WYŚWIETLANIE LISTY ŁOWISK Z FILTRAMI (CRUD - Read / Index
     * Obsługuje wyszukiwanie i filtrowanie łowisk według:
     * - Miejscowości
     * - Typu łowiska
     * - Dominującego gatunku ryby
     * - Minimalnej średniej oceny
     * Zwraca dane z paginacją.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Pobranie wszystkich ryb z bazy do selektora filtrów
        $allFishes = FishSpecies::orderBy('name')->get();

        // Pobranie łowisk z ich relacjami oraz obliczeniem średniej oceny
        $spots = FishingSpot::with(['fish_species', 'reviews'])
                            ->withAvg('reviews', 'rating')
                            ->get();

        // Filtr 1: Miejscowość (niezależny od wielkości liter)
        if ($request->filled('miejscowosc')) {
            $spots = $spots->filter(function($spot) use ($request) {
                return stripos($spot->city ?? '', $request->miejscowosc) !== false;
            });
        }

        // Filtr 2: Typ łowiska (jezioro/rzeka/zalew)
        if ($request->filled('typ') && $request->typ != 'Wszystkie') {
            $spots = $spots->filter(function($spot) use ($request) {
                return strtolower($spot->type) === strtolower($request->typ);
            });
        }

        // Filtr 3: Gatunek ryby występujący w łowisku
        if ($request->filled('gatunek') && $request->gatunek != 'Wszystkie') {
            $spots = $spots->filter(function($spot) use ($request) {
                return $spot->fish_species->contains('name', $request->gatunek);
            });
        }

        // Filtr 4: Ocena minimalna (porównanie ze średnią ocen)
        if ($request->filled('ocena') && $request->ocena != 'Wszystkie') {
            $minRating = (float)$request->ocena;
            $spots = $spots->filter(function($spot) use ($minRating) {
                // Łowiska bez opinii są traktowane jako 0
                $rating = $spot->reviews_avg_rating ?? 0;
                return $rating >= $minRating;
            });
        }

        // Paginacja
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $perPage = 10;

        $spots = new LengthAwarePaginator(
            $spots->forPage($currentPage, $perPage),
            $spots->count(),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        return view('lowiska', compact('spots', 'allFishes'));
    }

    /**
     * SZCZEGÓŁY POJEDYNCZEGO ŁOWISKA (CRUD - Read / Show)
     * Wyświetla pełne dane o wybranym łowisku, w tym:
     * - Występujące gatunki ryb
     * - Udogodnienia
     * - Zatwierdzone rekordy ryb
     * - Wszystkie opinie i oceny
     * 
     * @param  int  $id  Identyfikator łowiska
     * @param  string|null  $slug  Przyjazny URL
     * @return \Illuminate\View\View
     */
    public function show($id, $slug = null)
    {
        // Pobranie łowiska z relacjami, filtrując rekordy ryb do tylko zatwierdzonych przez admina
        $spot = FishingSpot::with(['fish_species', 'reviews', 'amenities', 'fish_records' => function($query) {
            $query->where('status', 'approved');
        }])->findOrFail($id);

        // Wyliczenie średniej oceny do wyświetlenia w widoku
        $avgRating = $spot->reviews->count() > 0 ? number_format($spot->reviews->avg('rating'), 1) : '0.0';

        // Pobranie wszystkich udogodnień i gatunków ryb dla edycji
        $allAmenities = \App\Models\Amenity::orderBy('name')->get();
        $allSpecies = \App\Models\FishSpecies::orderBy('name')->get();

        return view('szczegoly', compact('spot', 'avgRating', 'allAmenities', 'allSpecies'));
    }

    /**
     * INTERAKTYWNA MAPA ŁOWISK (Leaflet / Nominatim)
     * Ładuje mapę i przesyła przefiltrowane dane o łowiskach w formacie JSON bezpośrednio do mapy.
     * Dodatkowo obsługuje filtry odległościowe (promień) w połączeniu z Nominatim API.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function map(Request $request)
    {
        // Pobranie listy gatunków ryb do filtrów
        $allFishes = FishSpecies::orderBy('name')->get();

        // Pobranie wszystkich łowisk wraz ze średnimi ocenami
        $spots = FishingSpot::with(['fish_species', 'reviews'])
                            ->withAvg('reviews', 'rating')
                            ->get();

        // Filtr Miejscowość
        if ($request->filled('miejscowosc') && empty($request->zasieg)) {
            $spots = $spots->filter(function($spot) use ($request) {
                return stripos($spot->city ?? '', $request->miejscowosc) !== false;
            });
        }

        // Filtr Typ łowiska
        if ($request->filled('typ') && $request->typ != 'Wszystkie') {
            $spots = $spots->filter(function($spot) use ($request) {
                return strtolower($spot->type) === strtolower($request->typ);
            });
        }

        // Filtr Gatunek ryby
        if ($request->filled('gatunek') && $request->gatunek != 'Wszystkie') {
            $spots = $spots->filter(function($spot) use ($request) {
                return $spot->fish_species->contains('name', $request->gatunek);
            });
        }

        // Filtr Ocena minimalna
        if ($request->filled('ocena') && $request->ocena != 'Wszystkie') {
            $minRating = (float)$request->ocena;
            $spots = $spots->filter(function($spot) use ($minRating) {
                $rating = $spot->reviews_avg_rating ?? 0;
                return $rating >= $minRating;
            });
        }

        $spots = $spots->values();

        return view('mapa', compact('spots', 'allFishes'));
    }


    /**
     * ZGŁASZANIE NOWEGO REKORDU RYBY (CRUD - Store)
     * Umożliwia zalogowanemu wędkarzowi zgłoszenie złowionego okazu.
     * Wymaga podania nazwy ryby, wagi, długości, zdjęcia oraz daty.
     * Zgłoszenie trafia do bazy ze statusem 'pending' i oczekuje na weryfikację admina.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  Identyfikator łowiska
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeRecord(Request $request, $id)
    {
        // Walidacja formularza zgłoszeniowego
        $validated = $request->validate([
            'fish_name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0.01',
            'length' => 'required|integer|min:1',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // maks 5MB
            'caught_at' => 'required|date|before_or_equal:today', // data nie z przyszłości
        ]);

        $spot = FishingSpot::findOrFail($id);

        // Obsługa zapisu pliku zdjęcia rekordu ryby
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            $destinationPath = public_path('uploads/records');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $file->move($destinationPath, $filename);
            $photoUrl = '/uploads/records/' . $filename;
        } else {
            $photoUrl = 'https://images.unsplash.com/photo-1544983226-9d32b575eaee?w=800&q=80';
        }

        // Zapis w bazie danych
        \App\Models\FishRecord::create([
            'user_id' => auth()->id(),
            'fishing_spot_id' => $spot->id,
            'fish_name' => $validated['fish_name'],
            'weight' => $validated['weight'],
            'length' => $validated['length'],
            'photo_url' => $photoUrl,
            'caught_at' => $validated['caught_at'],
            'status' => 'pending', // Oczekiwanie na akceptację admina
        ]);

        return redirect()->back()->with('success_record', 'Twój rekord został pomyślnie zgłoszony i oczekuje na weryfikację przez administratora!');
    }

    /**
     * PRZEŁĄCZANIE ULUBIONYCH ŁOWISK (AJAX - Toggle Favorite)
     * Dodaje lub usuwa łowisko z listy ulubionych użytkownika.
     * Wykorzystuje metodę toggle() z systemu relacji Eloquent
     * i zwraca odpowiedź w formacie JSON do skryptu JS.
     * 
     * @param  int  $id  Identyfikator łowiska
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleFavorite($id)
    {
        $spot = FishingSpot::findOrFail($id);
        $user = auth()->user();

        // Metoda toggle() dodaje rekord jeśli go nie ma, lub usuwa jeśli już istnieje
        $result = $user->favorite_spots()->toggle($spot->id);
        
        // Jeśli id łowiska znalazło się w tablicy 'attached', oznacza to, że zostało dodane
        $isFavorite = in_array($spot->id, $result['attached']);

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite,
            'message' => $isFavorite ? 'Dodano do ulubionych' : 'Usunięto z ulubionych'
        ]);
    }

    /**
     * DODAWANIE NOWEJ OPINII (CRUD - Store)
     * Umożliwia zalogowanemu wędkarzowi dodanie opinii wraz z oceną.
     * Wymaga podania rating (1-5) oraz komentarza.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  Identyfikator łowiska
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeReview(Request $request, $id)
    {
        $validated = $request->validate([
            'rating' => 'required|numeric|min:0.5|max:5',
            'comment' => 'required|string|min:5|max:1000',
        ]);

        $spot = FishingSpot::findOrFail($id);

        // Zapis w bazie danych
        \App\Models\Review::create([
            'user_id' => auth()->id(),
            'fishing_spot_id' => $spot->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return redirect()->back()->with('success_review', 'Twoja opinia została pomyślnie dodana!');
    }
}

