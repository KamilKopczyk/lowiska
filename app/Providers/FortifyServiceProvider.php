<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

/**
 * Dostawca usług uwierzytelniania Fortify.
 * Odpowiada za konfigurację akcji backendowych Fortify,
 * mapowanie widoków uwierzytelniania oraz narzucanie limitów zapytań.
 */
class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Rejestracja usług.
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicjalizacja usług uwierzytelniania.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();
    }

    /**
     * Konfiguracja logiki biznesowej Fortify.
     */
    private function configureActions(): void
    {
        // Klasa odpowiedzialna za resetowanie hasła użytkownika
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        
        // Klasa odpowiedzialna za walidację i rejestrację nowego użytkownika
        Fortify::createUsersUsing(CreateNewUser::class);
    }

    /**
     * Konfiguracja widoków dla procesów Fortify.
     */
    private function configureViews(): void
    {
        Fortify::loginView(fn () => view('pages::auth.login'));
        Fortify::verifyEmailView(fn () => view('pages::auth.verify-email'));
        Fortify::twoFactorChallengeView(fn () => view('pages::auth.two-factor-challenge'));
        Fortify::confirmPasswordView(fn () => view('pages::auth.confirm-password'));
        Fortify::registerView(fn () => view('pages::auth.register'));
        Fortify::resetPasswordView(fn () => view('pages::auth.reset-password'));
        Fortify::requestPasswordResetLinkView(fn () => view('pages::auth.forgot-password'));
    }

    /**
     * Konfiguracja limitów prób logowania w celu ochrony przed atakami typu Brute Force.
     */
    private function configureRateLimiting(): void
    {
        // Limit dla wyzwania dwuetapowego (2FA) - maksymalnie 5 prób na minutę
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        // Limit dla standardowego logowania - maksymalnie 5 prób na minutę na parę e-mail+IP
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}

