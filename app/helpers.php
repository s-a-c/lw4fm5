<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Vite;

/**
 * Get the CSP nonce for the current request.
 */
function csp_nonce(): string
{
    return Vite::useCspNonce();
}
