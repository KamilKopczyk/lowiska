<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Uruchom migracje.
     */
    public function up(): void
    {
        Schema::create('fishing_spot_fish', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fishing_spot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fish_species_id')->constrained()->cascadeOnDelete();
            $table->integer('percentage')->default(0);
            $table->timestamps();
});
    }

    /**
     * Cofnij migracje.
     */
    public function down(): void
    {
        Schema::dropIfExists('fishing_spot_fish');
    }
};
