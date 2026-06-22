<?php

use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('strona rejestracji renderuje się poprawnie', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('nowy użytkownik może się zarejestrować', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Jan Kowalski',
        'email' => 'test@przykład.pl',
        'password' => 'haslo1234',
        'password_confirmation' => 'haslo1234',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});