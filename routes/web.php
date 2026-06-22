<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FishingSpotController;
use App\Http\Controllers\AdminController;

/**
 * =========================================================================
 * WĘDKARSKI RADAR - DEFINICJA TRAS
 * =========================================================================
 * Poniższy plik definiuje wszystkie punkty wejścia aplikacji.
 * Trasy są posegregowane tematycznie: trasy publiczne, trasy profilu użytkownika
 * oraz trasy administracyjne.
 */

// -------------------------------------------------------------------------
// TRASY PUBLICZNE
// -------------------------------------------------------------------------

// Strona główna portalu (Wędkarski Radar - Top 3 rekomendowane łowiska)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Wyświetlanie listy wszystkich łowisk z filtrowaniem i wyszukiwarką
Route::get('/lowiska', [FishingSpotController::class, 'index']);

// Widok szczegółowy wybranego łowiska 
Route::get('/lowiska/{id}-{slug}', [FishingSpotController::class, 'show']);

// Interaktywna mapa z wizualizacją łowisk i filtrem odległościowym
Route::get('/mapa', [FishingSpotController::class, 'map']);

// Trasa panelu /dashboard dostępna dla zalogowanych wędkarzy
Route::get('/dashboard', [HomeController::class, 'index'])->middleware(['auth'])->name('dashboard');

// -------------------------------------------------------------------------
// TRASY UWIERZYTELNIONE
// -------------------------------------------------------------------------
Route::middleware('auth')->group(function () {

    /**
     * Profil użytkownika / Pulpit
     * Obsługuje dwa tryby wyświetlania:
     * 1. Dla administratora: statystyki, dodawanie łowisk, panel weryfikacji rekordów i opinii.
     * 2. Dla zwykłego wędkarza: jego własne opinie, zgłoszone rekordy oraz ulubione łowiska.
     */
    Route::get('/profil', function () {
        $user = auth()->user();

        // Gdy zalogowany użytkownik to administrator
        if ($user->isAdmin()) {
            $spots = \App\Models\FishingSpot::with('reviews')->paginate(12, ['*'], 'spots_page');
            $reviews = \App\Models\Review::with(['fishing_spot', 'user'])->latest()->paginate(12, ['*'], 'reviews_page');
            $pendingRecords = \App\Models\FishRecord::with(['fishing_spot', 'user'])
                ->where('status', 'pending')
                ->latest()
                ->paginate(12, ['*'], 'records_page');
            $allAmenities = \App\Models\Amenity::orderBy('name')->get();
            $allSpecies = \App\Models\FishSpecies::orderBy('name')->get();
                
            return view('admin_dashboard', compact('spots', 'reviews', 'pendingRecords', 'allAmenities', 'allSpecies'));
        }

        // Gdy zalogowany użytkownik to standardowy wędkarz
        // Opinie napisane przez wędkarza
        $reviews = \App\Models\Review::with('fishing_spot')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(12, ['*'], 'reviews_page');

        // Rekordy zgłoszone przez wędkarza 
        $records = \App\Models\FishRecord::with('fishing_spot')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(12, ['*'], 'records_page');

        // Ulubione łowiska zapisane w profilu
        $favoriteSpots = $user->favorite_spots()->latest()->paginate(12, ['*'], 'spots_page');

        return view('profil', compact('reviews', 'records', 'favoriteSpots'));
    })->name('profil');

    // Zgłoszenie nowego rekordu ryby na wybranym łowisku
    Route::post('/lowiska/{id}/rekordy', [FishingSpotController::class, 'storeRecord'])->name('spots.records.store');

    // Obsługa dodawania/usuwania łowiska z ulubionych
    Route::post('/lowiska/{id}/toggle-favorite', [FishingSpotController::class, 'toggleFavorite'])->name('spots.toggle-favorite');

    // Dodawanie opinii do łowiska
    Route::post('/lowiska/{id}/opinie', [FishingSpotController::class, 'storeReview'])->name('spots.reviews.store');
});

require __DIR__.'/settings.php';


// -------------------------------------------------------------------------
// TRASY ADMINISTRATORA
// -------------------------------------------------------------------------
Route::middleware(['auth', 'can:is-admin'])->prefix('admin')->group(function () {
    // Dodawanie nowego łowiska do bazy danych
    Route::post('/lowiska', [AdminController::class, 'storeSpot'])->name('admin.spots.store');
    
    // Aktualizacja istniejącego łowiska
    Route::put('/lowiska/{id}', [AdminController::class, 'updateSpot'])->name('admin.spots.update');
    
    // Usuwanie istniejącego łowiska
    Route::delete('/lowiska/{id}', [AdminController::class, 'destroySpot'])->name('admin.spots.destroy');
    
    // Usuwanie nieodpowiednich opinii
    Route::delete('/opinie/{id}', [AdminController::class, 'destroyReview'])->name('admin.reviews.destroy');
    
    // Akceptacja nadesłanego przez wędkarza rekordu ryby
    Route::post('/rekordy/{id}/approve', [AdminController::class, 'approveRecord'])->name('admin.records.approve');
    
    // Odrzucenie nadesłanego przez wędkarza rekordu ryby
    Route::post('/rekordy/{id}/reject', [AdminController::class, 'rejectRecord'])->name('admin.records.reject');
});

