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
        Schema::create('fish_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fishing_spot_id')->constrained()->cascadeOnDelete();
            $table->string('fish_name');
            $table->decimal('weight', 8, 2);
            $table->integer('length');
            $table->string('photo_url')->nullable();
            $table->string('status')->default('pending');
            $table->date('caught_at');
            $table->timestamps();
});
    }

    /**
     * Cofnij migracje.
     */
    public function down(): void
    {
        Schema::dropIfExists('fish_records');
    }
};
