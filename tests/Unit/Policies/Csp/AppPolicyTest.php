<?php

declare(strict_types=1);

use App\Policies\Csp\AppPolicy;

test('app policy denies every operation', function (): void {
    $policy = new AppPolicy();
    $methods = [
        'viewAny',
        'view',
        'create',
        'update',
        'delete',
        'restore',
        'forceDelete',
    ];

    foreach ($methods as $method) {
        expect(call_user_func([$policy, $method]))->toBeFalse();
    }
});
