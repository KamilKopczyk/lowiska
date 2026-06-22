<?php

use App\Models\User;

test('strona potwierdzania hasła renderuje się poprawnie', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('password.confirm'));

    $response->assertOk();
});