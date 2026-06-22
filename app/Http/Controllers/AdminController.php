<?php

namespace App\Http\Controllers;

use App\Models\FishingSpot;
use App\Models\Review;
use App\Models\FishRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Kontroler panelu administratora.
 * Zarządza operacjami uprzywilejowanymi: dodawaniem/usuwaniem łowisk,
 * usuwaniem nieodpowiednich opinii oraz weryfikacją (akceptowaniem/odrzucaniem) zgłaszanych rekordów ryb.
 * Wszystkie akcje wymagają autoryzacji bramką 'is-admin'.
 */
class AdminController extends Controller
{
    /**
     * ZAPIS NOWEGO ŁOWISKA (CRUD - Store)
     * Obsługuje formularz dodawania nowego łowiska. Waliduje dane wejściowe,
     * zapisuje wgrany plik graficzny w folderze publicznym, a następnie
     * tworzy nowy rekord łowiska w bazie danych.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeSpot(Request $request)
    {
        // Sprawdzenie uprawnień administratora
        Gate::authorize('is-admin');

        // Walidacja danych wejściowych
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:jezioro,rzeka,zalew',
            'city' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'main_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'amenities' => 'required|array|min:1',
            'fish_species_check' => 'required|array|min:1',
        ], [
            'amenities.required' => 'Musisz wybrać co najmniej jedno udogodnienie.',
            'fish_species_check.required' => 'Musisz wybrać co najmniej jeden gatunek ryby w zarybieniu.',
        ]);

        // Dodatkowa walidacja struktury zarybienia (suma procentowa <= 100% oraz brak ryb z 0%)
        $checkedSpecies = $request->input('fish_species_check', []);
        $percentages = $request->input('fish_species_pct', []);
        $totalPct = 0;
        foreach ($checkedSpecies as $speciesId) {
            $pct = isset($percentages[$speciesId]) ? (int)$percentages[$speciesId] : 0;
            if ($pct <= 0) {
                return redirect()->back()
                    ->withErrors(['fish_species_check' => 'Udział procentowy dla każdego wybranego gatunku ryby musi być większy niż 0%.'])
                    ->withInput();
            }
            $totalPct += max(0, min(100, $pct));
        }

        if ($totalPct > 100) {
            return redirect()->back()
                ->withErrors(['fish_species_check' => 'Suma udziałów procentowych ryb nie może przekraczać 100% (obecnie wprowadzono: ' . $totalPct . '%).'])
                ->withInput();
        }

        // Obsługa wgrywania zdjęcia głównego łowiska
        if ($request->hasFile('main_image')) {
            $file = $request->file('main_image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            $destinationPath = public_path('uploads/spots');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $file->move($destinationPath, $filename);
            $validated['main_image_url'] = '/uploads/spots/' . $filename;
        } else {
            // Zapasowy link w przypadku braku przesłanego zdjęcia
            $validated['main_image_url'] = 'https://images.unsplash.com/photo-1506140502283-0571daae90d0?w=800&q=80';
        }

        // Usunięcie surowych pól, aby nie psuły zapisu w modelu Eloquent
        unset($validated['main_image']);
        unset($validated['amenities']);
        unset($validated['fish_species_check']);

        // Utworzenie łowiska w bazie
        $spot = FishingSpot::create($validated);

        // Zapisanie udogodnień
        $amenityIds = $request->input('amenities', []);
        $spot->amenities()->attach($amenityIds);

        // Zapisanie gatunków ryb z pivotem 'percentage'
        $fishSpeciesSyncData = [];
        foreach ($checkedSpecies as $speciesId) {
            $pct = isset($percentages[$speciesId]) ? (int)$percentages[$speciesId] : 0;
            $fishSpeciesSyncData[$speciesId] = ['percentage' => max(0, min(100, $pct))];
        }
        $spot->fish_species()->attach($fishSpeciesSyncData);

        return redirect()->route('profil')->with('success', 'Łowisko zostało pomyślnie dodane ze zdjęciem, udogodnieniami i zarybieniem!');
    }

    /**
     * AKTUALIZACJA ŁOWISKA (CRUD - Update)
     * Obsługuje formularz edycji łowiska. Waliduje dane wejściowe,
     * opcjonalnie zapisuje wgrany plik graficzny w folderze publicznym,
     * aktualizuje rekord łowiska w bazie danych oraz synchronizuje relacje
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSpot(Request $request, $id)
    {
        // Sprawdzenie uprawnień administratora
        Gate::authorize('is-admin');

        $spot = FishingSpot::findOrFail($id);

        // Walidacja danych wejściowych
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:jezioro,rzeka,zalew',
            'city' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'main_image_url' => 'required|string|max:1000',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'amenities' => 'required|array|min:1',
            'fish_species_check' => 'required|array|min:1',
        ], [
            'amenities.required' => 'Musisz wybrać co najmniej jedno udogodnienie.',
            'fish_species_check.required' => 'Musisz wybrać co najmniej jeden gatunek ryby w zarybieniu.',
        ]);

        // Dodatkowa walidacja struktury zarybienia (suma procentowa <= 100% oraz brak ryb z 0%)
        $checkedSpecies = $request->input('fish_species_check', []);
        $percentages = $request->input('fish_species_pct', []);
        $totalPct = 0;
        foreach ($checkedSpecies as $speciesId) {
            $pct = isset($percentages[$speciesId]) ? (int)$percentages[$speciesId] : 0;
            if ($pct <= 0) {
                return redirect()->back()
                    ->withErrors(['fish_species_check' => 'Udział procentowy dla każdego wybranego gatunku ryby musi być większy niż 0%.'])
                    ->withInput();
            }
            $totalPct += max(0, min(100, $pct));
        }

        if ($totalPct > 100) {
            return redirect()->back()
                ->withErrors(['fish_species_check' => 'Suma udziałów procentowych ryb nie może przekraczać 100% (obecnie wprowadzono: ' . $totalPct . '%).'])
                ->withInput();
        }

        // Obsługa wgrywania zdjęcia głównego łowiska
        if ($request->hasFile('main_image')) {
            $file = $request->file('main_image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            $destinationPath = public_path('uploads/spots');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $file->move($destinationPath, $filename);
            $validated['main_image_url'] = '/uploads/spots/' . $filename;
        }

        // Usunięcie surowych pól, aby nie psuły zapisu w modelu Eloquent
        unset($validated['main_image']);
        unset($validated['amenities']);
        unset($validated['fish_species_check']);

        // Aktualizacja łowiska w bazie
        $spot->update($validated);

        // Synchronizacja udogodnień
        $amenityIds = $request->input('amenities', []);
        $spot->amenities()->sync($amenityIds);

        // Synchronizacja gatunków ryb z pivotem 'percentage'
        $fishSpeciesSyncData = [];
        foreach ($checkedSpecies as $speciesId) {
            $pct = isset($percentages[$speciesId]) ? (int)$percentages[$speciesId] : 0;
            $fishSpeciesSyncData[$speciesId] = ['percentage' => max(0, min(100, $pct))];
        }
        $spot->fish_species()->sync($fishSpeciesSyncData);

        return redirect()->back()->with('success', 'Łowisko zostało pomyślnie zaktualizowane!');
    }

    /**
     * USUWANIE ŁOWISKA (CRUD - Destroy)
     * Trwale usuwa wskazane łowisko z bazy danych wraz z powiązanymi relacjami.
     * 
     * @param  int  $id  Identyfikator łowiska
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroySpot($id)
    {
        Gate::authorize('is-admin');

        $spot = FishingSpot::findOrFail($id);
        $spot->delete();

        return redirect()->route('profil')->with('success', 'Łowisko zostało usunięte!');
    }

    /**
     * USUWANIE OPINII UŻYTKOWNIKA (CRUD - Destroy)
     * Pozwala administratorowi na usunięcie opinii łowiska.
     * 
     * @param  int  $id  Identyfikator opinii
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyReview($id)
    {
        Gate::authorize('is-admin');

        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->route('profil')->with('success', 'Opinia została usunięta!');
    }

    /**
     * AKCEPTOWANIE REKORDU RYBY (CRUD - Update)
     * Zmienia status zgłoszonego rekordu z 'pending' na 'approved',
     * dzięki czemu rekord staje się publicznie widoczny na profilu łowiska.
     * 
     * @param  int  $id  Identyfikator rekordu ryby
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveRecord($id)
    {
        Gate::authorize('is-admin');

        $record = FishRecord::findOrFail($id);
        $record->update(['status' => 'approved']);

        return redirect()->route('profil')->with('success', 'Rekord został zaakceptowany!');
    }

    /**
     * ODRZUCANIE REKORDU RYBY (CRUD - Update)
     * Zmienia status zgłoszonego rekordu na 'rejected'.
     * 
     * @param  int  $id  Identyfikator rekordu ryby
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectRecord($id)
    {
        Gate::authorize('is-admin');

        $record = FishRecord::findOrFail($id);
        $record->update(['status' => 'rejected']);

        return redirect()->route('profil')->with('success', 'Rekord został odrzucony!');
    }
}

