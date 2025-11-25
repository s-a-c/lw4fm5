<?php

declare(strict_types=1);

use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;

test('example', function (): void {
    /** @var TestResponse<Response> $response */
    $response = $this->get('/');

    $response->assertOk();
});
