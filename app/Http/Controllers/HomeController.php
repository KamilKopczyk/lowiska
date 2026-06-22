<?php

namespace App\Http\Controllers;

use App\Models\FishingSpot;

/**
 * Kontroler obsługujący stronę główną.
 * Odpowiada za dostarczanie podstawowych danych, takich jak rekomendowane łowiska.
 */
class HomeController extends Controller
{
    /**
     * Wyświetla stronę główną portalu.
     * Pobiera 3 najwyżej oceniane łowiska wraz z przypisanymi gatunkami ryb.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Rekomendowane łowiska na podstawie średniej oceny użytkowników
        $recommendedSpots = FishingSpot::with('fish_species')
            ->withAvg('reviews', 'rating')
            ->orderByRaw('COALESCE(reviews_avg_rating, 0) DESC')
            ->take(3)
            ->get();

        return view('dashboard', compact('recommendedSpots'));
    }
}

