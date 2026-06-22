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
        Schema::create('fishing_spots', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['jezioro', 'rzeka', 'zalew']);
            $table->string('city');
            $table->string('region');
            $table->text('description')->nullable();
            $table->decimal('lat', 10, 8);
            $table->decimal('lng', 11, 8);
            $table->string('main_image_url')->nullable();
            $table->timestamps();
});
    }

    /**
     * Cofnij migracje.
     */
    public function down(): void
    {
        Schema::dropIfExists('fishing_spots');
    }
};
