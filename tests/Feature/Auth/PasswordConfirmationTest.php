<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;

test('confirm password screen can be rendered', function (): void {
    /** @phpstan-var Tests\TestCase $this */
    $user = User::factory()->create();

    /** @phpstan-var TestResponse<Response> $response */
    $response = $this->actingAs($user)->get(route('password.confirm'));

    expect($response)->assertOk();
});
