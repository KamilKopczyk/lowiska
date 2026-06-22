<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Łowiska (FishingSpot).
 * Główny model aplikacji reprezentujący akwen wodny wraz z jego współrzędnymi
 * geograficznymi, opisem i powiązanymi relacjami (gatunki ryb, udogodnienia, opinie, rekordy okazów).
 */
class FishingSpot extends Model
{
    // Blokada masowego przypisywania
    protected $guarded = [];

    /**
     * Relacja do gatunków ryb występujących w łowisku.
     * Tabela łącznikowa: 'fishing_spot_fish'
     * Zawiera pole 'percentage' (procentowy udział gatunku w zarybieniu).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function fish_species()
    {
        return $this->belongsToMany(FishSpecies::class, 'fishing_spot_fish')
                    ->withPivot('percentage');
    }

    /**
     * Relacja do udogodnień dostępnych na łowisku.
     * Tabela łącznikowa: 'amenity_fishing_spot'
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'amenity_fishing_spot');
    }

    /**
     * Relacja do opinii i komentarzy dodanych przez wędkarzy.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Relacja do zgłoszonych rekordów ryb na tym łowisku.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fish_records()
    {
        return $this->hasMany(FishRecord::class);
    }

    /**
     * Relacja określająca, którzy użytkownicy dodali to łowisko do swoich ulubionych.
     * Tabela łącznikowa: 'fishing_spot_user' z automatycznym zapisem dat.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favorited_by_users()
    {
        return $this->belongsToMany(User::class, 'fishing_spot_user')->withTimestamps();
    }
}

