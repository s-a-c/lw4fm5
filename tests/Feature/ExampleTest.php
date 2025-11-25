<?php

declare(strict_types=1);

use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;

test('returns a successful response', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    /** @var TestResponse<Response> $response */
    $response = $this->get(route('home'));

    $response->assertOk();
});
