<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

/**
 * =========================================================================
 * TRASY USTAWIEŃ UŻYTKOWNIKA
 * =========================================================================
 * Trasy obsługujące konfigurację konta, wygląd oraz zabezpieczenia profilu.
 * Wykorzystują komponenty Livewire.
 */

Route::middleware(['auth'])->group(function () {
    // Automatyczne przekierowanie z /settings do edycji profilu
    Route::redirect('settings', 'settings/profile');

    // Edycja danych profilowych
    Route::livewire('settings/profile', 'pages::settings.profile')->name('profile.edit');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Zmiana wyglądu strony
    Route::livewire('settings/appearance', 'pages::settings.appearance')->name('appearance.edit');

    // Bezpieczeństwo konta
    Route::livewire('settings/security', 'pages::settings.security')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('security.edit');
});

