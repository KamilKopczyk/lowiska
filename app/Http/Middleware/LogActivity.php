<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pośrednik rejestracji aktywności.
 * Przechwytuje każde przychodzące żądanie HTTP do aplikacji,
 * rejestrując metodę żądania, adres URI oraz e-mail zalogowanego użytkownika lub status gościa
 * w celach analitycznych i audytowych.
 */
class LogActivity
{
    /**
     * Obsługa przychodzącego żądania HTTP.
     *
     * @param  \Illuminate\Http\Request  $request  Obiekt bieżącego żądania HTTP.
     * @param  \Closure  $next  Następny krok w Laravel.
     * @return \Symfony\Component\HttpFoundation\Response  Zwracana odpowiedź HTTP.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ogólny log oznaczony statusem 'notice' wysyłany na kanał standardowego wyjścia błędów
        Log::channel('stderr')->notice(
            "Użytkownik wywołał metodę {$request->method()} na ścieżce '{$request->path()}'"
        );

        // Log zapisywany do głównego pliku logów Laravel z określeniem tożsamości użytkownika
        $email = $request->user() ? $request->user()->email : 'anonymous';

        Log::channel('single')->notice(
            "Użytkownik '{$email}' wykonał operację {$request->method()} pod adresem '{$request->getRequestUri()}'"
        );

        return $next($request);
    }
}

