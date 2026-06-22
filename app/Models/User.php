<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * Model Użytkownika.
 * Reprezentuje wędkarza lub administratora serwisu.
 * Integruje funkcje uwierzytelniania, powiadomień, weryfikacji dwuetapowej (Fortify) i relacje.
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Konfiguracja rzutowania typów kolumn bazy danych.
     * 
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Generuje inicjały użytkownika na podstawie imienia i nazwiska.
     * Pobiera pierwsze litery z maksymalnie dwóch pierwszych wyrazów nazwy użytkownika.
     * Sposób użycia: $user->initials()
     * 
     * @return string Inicjały
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Sprawdza, czy użytkownik posiada uprawnienia administratora.
     * Rola administratora oznaczona jest wartością 'admin' w kolumnie 'role'.
     * Sposób użycia: $user->isAdmin()
     * 
     * @return bool True jeśli administrator, w przeciwnym razie False
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Relacja do opinii napisanych przez tego użytkownika.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Relacja do zgłoszonych przez tego użytkownika rekordów ryb.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fish_records()
    {
        return $this->hasMany(FishRecord::class);
    }

    /**
     * Relacja do łowisk, które ten użytkownik dodał do ulubionych.
     * Tabela łącznikowa: 'fishing_spot_user' z datą dodania.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favorite_spots()
    {
        return $this->belongsToMany(FishingSpot::class, 'fishing_spot_user')->withTimestamps();
    }
}


