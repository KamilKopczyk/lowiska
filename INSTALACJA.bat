@echo off
chcp 65001 >nul
title Wędkarski Radar - Instalacja projektu
color 0A

echo ============================================================
echo   WĘDKARSKI RADAR - Automatyczna instalacja projektu
echo ============================================================
echo.

:: -----------------------------------------------------------
:: 1. Sprawdzenie wymaganych narzędzi
:: -----------------------------------------------------------
echo [1/7] Sprawdzanie wymaganych narzędzi...
echo.

php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo [BŁĄD] PHP nie jest zainstalowane lub nie znajduje się w PATH.
    echo         Pobierz PHP 8.3+: https://www.php.net/downloads
    pause
    exit /b 1
)
echo   [OK] PHP znalezione:
php -v 2>&1 | findstr /i "PHP"
echo.

composer -V >nul 2>&1
if %errorlevel% neq 0 (
    echo [BŁĄD] Composer nie jest zainstalowany.
    echo         Pobierz Composer: https://getcomposer.org/download/
    pause
    exit /b 1
)
echo   [OK] Composer znaleziony:
composer -V 2>&1 | findstr /i "Composer"
echo.

node -v >nul 2>&1
if %errorlevel% neq 0 (
    echo [BŁĄD] Node.js nie jest zainstalowany.
    echo         Pobierz Node.js v20+: https://nodejs.org/
    pause
    exit /b 1
)
echo   [OK] Node.js znaleziony:
node -v
echo.

npm -v >nul 2>&1
if %errorlevel% neq 0 (
    echo [BŁĄD] npm nie jest zainstalowany.
    pause
    exit /b 1
)
echo   [OK] npm znaleziony:
npm -v
echo.

:: -----------------------------------------------------------
:: 2. Instalacja zależności PHP (Composer)
:: -----------------------------------------------------------
echo [2/7] Instalacja zależności PHP (composer install)...
echo.
call composer install --no-interaction
if %errorlevel% neq 0 (
    echo [BŁĄD] Composer install nie powiódł się.
    pause
    exit /b 1
)
echo.
echo   [OK] Zależności PHP zainstalowane.
echo.

:: -----------------------------------------------------------
:: 3. Instalacja zależności JavaScript (npm)
:: -----------------------------------------------------------
echo [3/7] Instalacja zależności JavaScript (npm install)...
echo.
call npm install
if %errorlevel% neq 0 (
    echo [BŁĄD] npm install nie powiódł się.
    pause
    exit /b 1
)
echo.
echo   [OK] Zależności JavaScript zainstalowane.
echo.

:: -----------------------------------------------------------
:: 4. Konfiguracja pliku .env
:: -----------------------------------------------------------
echo [4/7] Konfiguracja zmiennych środowiskowych (.env)...
echo.
if not exist ".env" (
    copy .env.example .env >nul
    echo   [OK] Utworzono plik .env na podstawie .env.example
) else (
    echo   [OK] Plik .env już istnieje, pomijam.
)
echo.

:: -----------------------------------------------------------
:: 5. Generowanie klucza aplikacji
:: -----------------------------------------------------------
echo [5/7] Generowanie klucza aplikacji Laravel...
echo.
call php artisan key:generate --ansi
echo.
echo   [OK] Klucz aplikacji wygenerowany.
echo.

:: -----------------------------------------------------------
:: 6. Migracje i dane testowe (Seed)
:: -----------------------------------------------------------
echo [6/7] Uruchamianie migracji i seedowania bazy danych...
echo.
call php artisan migrate --seed --force
if %errorlevel% neq 0 (
    echo [BŁĄD] Migracja bazy danych nie powiodła się.
    pause
    exit /b 1
)
echo.
echo   [OK] Baza danych SQLite utworzona i zaseedowana.
echo.

:: -----------------------------------------------------------
:: 7. Link symboliczny do plików publicznych
:: -----------------------------------------------------------
echo [7/7] Tworzenie linku symbolicznego storage...
echo.
call php artisan storage:link 2>nul
echo   [OK] Link symboliczny utworzony.
echo.

:: -----------------------------------------------------------
:: Podsumowanie
:: -----------------------------------------------------------
echo ============================================================
echo   INSTALACJA ZAKOŃCZONA POMYŚLNIE!
echo ============================================================
echo.
echo   Aby uruchomić projekt, otwórz DWA terminale:
echo.
echo   Terminal 1 (serwer PHP):
echo     php artisan serve
echo.
echo   Terminal 2 (kompilator Vite):
echo     npm run dev
echo.
echo   Aplikacja będzie dostępna pod adresem:
echo     http://localhost:8000
echo.
echo   Dane logowania:
echo     Admin:   admin@lowisko.pl / password
echo     Wędkarz: jan@lowisko.pl   / password
echo.
echo ============================================================
pause
