<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Rekordu Ryby (FishRecord).
 * Reprezentuje złowiony i zgłoszony przez użytkownika okaz ryby na konkretnym łowisku.
 * Zawiera weryfikowany status ('pending', 'approved', 'rejected') oraz parametry fizyczne ryby (waga, długość).
 */
class FishRecord extends Model
{
    protected $guarded = [];

    /**
     * Relacja do łowiska, na którym ryba została złowiona.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fishing_spot()
    {
        return $this->belongsTo(FishingSpot::class);
    }

    /**
     * Relacja do użytkownika, który zgłosił ten rekord.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Akcesor pobierający nazwę użytkownika.
     * Użycie: $record->user_name
     * 
     * @return string|null
     */
    public function getUserNameAttribute()
    {
        return $this->user ? $this->user->name : null;
    }

    /**
     * Oblicza pozycję (ranking) tego rekordu ryby na danym łowisku.
     * Ranking jest tworzony na podstawie wagi okazu (malejąco), a w drugiej kolejności długości (malejąco)
     * spośród wszystkich zatwierdzonych rekordów na tym samym łowisku.
     * 
     * @return int Pozycja w rankingu (od 1 wzwyż)
     */
    public function getRank(): int
    {
        $records = self::where('fishing_spot_id', $this->fishing_spot_id)
            ->where('status', 'approved')
            ->orderByDesc('weight')
            ->orderByDesc('length')
            ->pluck('id')
            ->toArray();

        $index = array_search($this->id, $records);

        return $index !== false ? ($index + 1) : 1;
    }
}
