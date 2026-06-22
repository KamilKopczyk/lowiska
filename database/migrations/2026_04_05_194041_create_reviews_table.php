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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fishing_spot_id')->constrained()->cascadeOnDelete();
            $table->decimal('rating', 2, 1);
            $table->text('comment');
            $table->timestamps();
        });
    }

    /**
     * Cofnij migracje.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
