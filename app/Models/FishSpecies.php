<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Gatunku Ryby (FishSpecies).
 * Reprezentuje gatunek ryby występujący w łowiskach.
 * Posiada relację wiele-do-wielu z modelami FishingSpot z dodatkowym polem 'percentage' w tabeli pivot.
 */
class FishSpecies extends Model
{
    protected $guarded = [];

    /**
     * Relacja do łowisk, w których występuje dany gatunek.
     * Tabela łącznikowa: 'fishing_spot_fish'
     * Przechowuje dodatkową informację o szacunkowym procencie zarybienia (pivot: percentage).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function fishing_spots()
    {
        return $this->belongsToMany(FishingSpot::class, 'fishing_spot_fish')
                    ->withPivot('percentage');
    }
}
