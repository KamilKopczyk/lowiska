<?php

use App\Models\User;
use App\Models\FishingSpot;
use App\Models\FishSpecies;
use App\Models\Amenity;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->amenities = Amenity::create(['name' => 'Parking']);
    $this->species1 = FishSpecies::create(['name' => 'Szczupak']);
    $this->species2 = FishSpecies::create(['name' => 'Karp']);
});

test('administrator może utworzyć łowisko z udogodnieniami i zarybieniem', function () {
    $this->actingAs($this->admin);

    // Symulacja przesłanego pliku
    $file = \Illuminate\Http\UploadedFile::fake()->create('test_spot.jpg', 100);

    $response = $this->post(route('admin.spots.store'), [
        'name' => 'Testowe Łowisko',
        'type' => 'jezioro',
        'city' => 'Kraków',
        'region' => 'małopolskie',
        'description' => 'Opis testowy',
        'lat' => 50.0647,
        'lng' => 19.9450,
        'main_image' => $file,
        'amenities' => [$this->amenities->id],
        'fish_species_check' => [$this->species1->id, $this->species2->id],
        'fish_species_pct' => [
            $this->species1->id => 40,
            $this->species2->id => 50
        ]
    ]);

    $response->assertRedirect(route('profil'));
    
    $spot = FishingSpot::where('name', 'Testowe Łowisko')->first();
    expect($spot)->not->toBeNull();
    expect($spot->amenities->pluck('id')->toArray())->toContain($this->amenities->id);
    
    // Weryfikacja wartości pivot (zarybienie)
    $species1Pivot = $spot->fish_species()->where('fish_species_id', $this->species1->id)->first()->pivot;
    $species2Pivot = $spot->fish_species()->where('fish_species_id', $this->species2->id)->first()->pivot;
    
    expect($species1Pivot->percentage)->toBe(40);
    expect($species2Pivot->percentage)->toBe(50);
});

test('administrator nie może utworzyć łowiska, jeśli suma procentowa zarybienia przekracza 100%', function () {
    $this->actingAs($this->admin);

    $file = \Illuminate\Http\UploadedFile::fake()->create('test_spot.jpg', 100);

    $response = $this->post(route('admin.spots.store'), [
        'name' => 'Przełowione Łowisko',
        'type' => 'jezioro',
        'city' => 'Kraków',
        'region' => 'małopolskie',
        'description' => 'Opis testowy',
        'lat' => 50.0647,
        'lng' => 19.9450,
        'main_image' => $file,
        'amenities' => [$this->amenities->id],
        'fish_species_check' => [$this->species1->id, $this->species2->id],
        'fish_species_pct' => [
            $this->species1->id => 60,
            $this->species2->id => 50
        ]
    ]);

    $response->assertSessionHasErrors(['fish_species_check']);
    expect(FishingSpot::where('name', 'Przełowione Łowisko')->first())->toBeNull();
});

test('administrator może zaktualizować łowisko z udogodnieniami i zarybieniem', function () {
    $this->actingAs($this->admin);

    $spot = FishingSpot::create([
        'name' => 'Stare Łowisko',
        'type' => 'jezioro',
        'city' => 'Kraków',
        'region' => 'małopolskie',
        'description' => 'Opis testowy',
        'lat' => 50.0647,
        'lng' => 19.9450,
        'main_image_url' => '/uploads/spots/old.jpg'
    ]);

    $response = $this->put(route('admin.spots.update', $spot->id), [
        'name' => 'Zaktualizowane Łowisko',
        'type' => 'jezioro',
        'city' => 'Kraków',
        'region' => 'małopolskie',
        'description' => 'Opis testowy',
        'lat' => 50.0647,
        'lng' => 19.9450,
        'main_image_url' => '/uploads/spots/old.jpg',
        'amenities' => [$this->amenities->id],
        'fish_species_check' => [$this->species1->id],
        'fish_species_pct' => [
            $this->species1->id => 80
        ]
    ]);

    $response->assertRedirect();
    
    $spot->refresh();
    expect($spot->name)->toBe('Zaktualizowane Łowisko');
    expect($spot->fish_species()->where('fish_species_id', $this->species1->id)->first()->pivot->percentage)->toBe(80);
});

test('administrator nie może zaktualizować łowiska, jeśli suma procentowa zarybienia przekracza 100%', function () {
    $this->actingAs($this->admin);

    $spot = FishingSpot::create([
        'name' => 'Stare Łowisko',
        'type' => 'jezioro',
        'city' => 'Kraków',
        'region' => 'małopolskie',
        'description' => 'Opis testowy',
        'lat' => 50.0647,
        'lng' => 19.9450,
        'main_image_url' => '/uploads/spots/old.jpg'
    ]);

    $response = $this->put(route('admin.spots.update', $spot->id), [
        'name' => 'Zaktualizowane Łowisko',
        'type' => 'jezioro',
        'city' => 'Kraków',
        'region' => 'małopolskie',
        'description' => 'Opis testowy',
        'lat' => 50.0647,
        'lng' => 19.9450,
        'main_image_url' => '/uploads/spots/old.jpg',
        'amenities' => [$this->amenities->id],
        'fish_species_check' => [$this->species1->id, $this->species2->id],
        'fish_species_pct' => [
            $this->species1->id => 80,
            $this->species2->id => 30
        ]
    ]);

    $response->assertSessionHasErrors(['fish_species_check']);
    $spot->refresh();
    expect($spot->name)->toBe('Stare Łowisko');
});

test('administrator nie może utworzyć łowiska, jeśli jakikolwiek procent zarybienia wynosi 0%', function () {
    $this->actingAs($this->admin);

    $file = \Illuminate\Http\UploadedFile::fake()->create('test_spot.jpg', 100);

    $response = $this->post(route('admin.spots.store'), [
        'name' => 'Łowisko z zerem',
        'type' => 'jezioro',
        'city' => 'Kraków',
        'region' => 'małopolskie',
        'description' => 'Opis testowy',
        'lat' => 50.0647,
        'lng' => 19.9450,
        'main_image' => $file,
        'amenities' => [$this->amenities->id],
        'fish_species_check' => [$this->species1->id, $this->species2->id],
        'fish_species_pct' => [
            $this->species1->id => 40,
            $this->species2->id => 0
        ]
    ]);

    $response->assertSessionHasErrors(['fish_species_check']);
    expect(FishingSpot::where('name', 'Łowisko z zerem')->first())->toBeNull();
});

test('administrator nie może zaktualizować łowiska, jeśli jakikolwiek procent zarybienia wynosi 0%', function () {
    $this->actingAs($this->admin);

    $spot = FishingSpot::create([
        'name' => 'Stare Łowisko',
        'type' => 'jezioro',
        'city' => 'Kraków',
        'region' => 'małopolskie',
        'description' => 'Opis testowy',
        'lat' => 50.0647,
        'lng' => 19.9450,
        'main_image_url' => '/uploads/spots/old.jpg'
    ]);

    $response = $this->put(route('admin.spots.update', $spot->id), [
        'name' => 'Zaktualizowane Łowisko',
        'type' => 'jezioro',
        'city' => 'Kraków',
        'region' => 'małopolskie',
        'description' => 'Opis testowy',
        'lat' => 50.0647,
        'lng' => 19.9450,
        'main_image_url' => '/uploads/spots/old.jpg',
        'amenities' => [$this->amenities->id],
        'fish_species_check' => [$this->species1->id, $this->species2->id],
        'fish_species_pct' => [
            $this->species1->id => 50,
            $this->species2->id => 0
        ]
    ]);

    $response->assertSessionHasErrors(['fish_species_check']);
    $spot->refresh();
    expect($spot->name)->toBe('Stare Łowisko');
});
