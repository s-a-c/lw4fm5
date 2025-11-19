<?php

declare(strict_types=1);

// TODO: Re-enable when Pest Architecture plugin bug is fixed
// See: https://github.com/pestphp/pest-plugin-arch/issues
// arch()->preset()->php();
// arch()->preset()->strict()
//     ->ignoring('App\Http\Controllers\Controller');
/** @phpstan-ignore-next-line */
arch()->preset()->security();

/** @phpstan-ignore-next-line */
arch('controllers')
    ->expect('App\Http\Controllers')
    ->not->toBeUsed();

//
