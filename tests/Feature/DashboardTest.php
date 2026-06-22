<?php

use App\Models\User;

test('goście są przekierowywani na stronę logowania', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('zalogowany użytkownik może wejść na dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});