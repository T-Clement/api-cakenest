<?php

use function Pest\Laravel\postJson;

test('new users can register', function () {
    $response = postJson('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'is_admin' => 1,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    // dd($response);

    $this->assertAuthenticated();
    $response->assertNoContent();

    
});
