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
        Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('icon')->nullable();
             $table->timestamps();
});
    }

    /**
     * Cofnij migracje.
     */
    public function down(): void
    {
        Schema::dropIfExists('amenities');
    }
};
