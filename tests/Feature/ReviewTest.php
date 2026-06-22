<?php

use App\Models\User;
use App\Models\FishingSpot;
use App\Models\Review;

test('zalogowany użytkownik może dodać opinię i zostaje ona zapisana', function () {
    $user = User::factory()->create();
    $spot = FishingSpot::create([
        'name' => 'Jezioro Testowe',
        'type' => 'jezioro',
        'city' => 'Gdańsk',
        'region' => 'pomorskie',
        'description' => 'Opis testowy',
        'lat' => 54.3520,
        'lng' => 18.6466,
        'main_image_url' => '/uploads/spots/test.jpg'
    ]);

    $this->actingAs($user);

    $response = $this->post(route('spots.reviews.store', $spot->id), [
        'rating' => 4.5,
        'comment' => 'Wspaniałe łowisko, polecam każdemu!'
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success_review');

    $review = Review::where('fishing_spot_id', $spot->id)->first();
    expect($review)->not->toBeNull();
    expect($review->rating)->toEqual(4.5);
    expect($review->comment)->toBe('Wspaniałe łowisko, polecam każdemu!');
    expect($review->user_id)->toBe($user->id);
});
