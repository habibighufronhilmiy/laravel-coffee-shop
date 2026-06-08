<?php

test('midtrans client key returns configured key', function () {
    $response = $this->getJson('/api/midtrans/client-key');

    $response->assertOk();
    expect($response->json('client_key'))->not->toBeNull();
});
