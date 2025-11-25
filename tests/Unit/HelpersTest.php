<?php

declare(strict_types=1);

test('csp_nonce returns a non-empty string', function (): void {
    $nonce = csp_nonce();

    expect($nonce)
        ->toBeString()
        ->and(mb_strlen($nonce))->toBeGreaterThan(0);
});

test('csp_nonce returns different values on subsequent calls', function (): void {
    $nonce1 = csp_nonce();
    $nonce2 = csp_nonce();

    // Nonces should be different (they're generated per request)
    expect($nonce1)->not->toBe($nonce2);
});
