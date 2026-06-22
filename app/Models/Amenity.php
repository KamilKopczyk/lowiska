<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Udogodnienia (Amenity).
 * Reprezentuje infrastrukturę lub usługi dostępne na łowisku.
 * Posiada relację wiele-do-wielu z modelami FishingSpot.
 */
class Amenity extends Model
{
    protected $guarded = [];

    /**
     * Relacja do łowisk, na których to udogodnienie jest dostępne.
     * Tabela łącznikowa: 'amenity_fishing_spot'
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function fishing_spots()
    {
        return $this->belongsToMany(FishingSpot::class, 'amenity_fishing_spot');
    }
}
