<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Opinii (Review).
 * Reprezentuje recenzję/opinię oraz ocenę (od 1 do 5 gwiazdek) wystawioną przez wędkarza dla danego łowiska.
 */
class Review extends Model
{
    protected $guarded = [];

    /**
     * Relacja do łowiska, którego dotyczy dana opinia.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fishing_spot()
    {
        return $this->belongsTo(FishingSpot::class);
    }

    /**
     * Relacja do użytkownika, który dodał tę opinię.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Akcesor pobierający nazwę autora opinii.
     * Ułatwia dostęp bezpośredni w szablonie blade: $review->user_name
     * 
     * @return string|null
     */
    public function getUserNameAttribute()
    {
        return $this->user ? $this->user->name : null;
    }

    /**
     * Akcesor mapujący komentarz na atrybut 'body'.
     * Alias do kolumny 'comment'.
     * Użycie: $review->body
     * 
     * @return string|null
     */
    public function getBodyAttribute()
    {
        return $this->comment;
    }
}

