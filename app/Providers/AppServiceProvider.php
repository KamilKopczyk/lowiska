<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

/**
 * Główny dostawca usług aplikacji.
 * Odpowiada za inicjalizację kluczowych komponentów systemu.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Rejestracja usług.
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicjalizacja usług aplikacji po ich zarejestrowaniu.
     */
    public function boot(): void
    {
        // Konfiguracja paginacji
        Paginator::useTailwind();

        // Definicja bramki autoryzacji 'is-admin' sprawdzającej, czy zalogowany użytkownik ma rolę administratora.
        \Illuminate\Support\Facades\Gate::define('is-admin', function (\App\Models\User $user) {
            return $user->isAdmin();
        });

        // Globalna reguła nadrzędna: Administrator automatycznie przechodzi pomyślnie wszystkie testy uprawnień.
        \Illuminate\Support\Facades\Gate::before(function (\App\Models\User $user, string $ability) {
            if ($user->isAdmin()) {
                return true;
            }
        });
    }
}

