<?php

declare(strict_types=1);

/**
 * Compliant with [AI-GUIDELINES.md](../../.ai/AI-GUIDELINES.md) v0921d4cfab198af1451ef177b6e47657b5d3ab0292f77bf232496291dee47183
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Filament Script Attributes
    |--------------------------------------------------------------------------
    |
    | Configure the HTML attributes applied to every Filament-managed script
    | tag. Boolean flags like "defer" or "async" map to the dedicated helper
    | methods on Filament's Js asset instances, while the "attributes" array
    | is merged directly into the tag as key/value pairs.
    |
    | targets: limit attribute mutations to script identifiers in the form
    |          "package:id". Use "*" to affect every Filament script.
    | exclude: script identifiers that should be marked as "loaded on request"
    |          so they are omitted entirely from the rendered output.
    */
    'scripts' => [
        'defer' => true,
        'async' => false,
        'attributes' => [
            'data-turbo-eval' => 'false',
        ],
        'targets' => ['*'],
        'exclude' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Alpine Component Loading
    |--------------------------------------------------------------------------
    |
    | Filament exposes Alpine components for interactive widgets. Disable them
    | if the host application supplies alternative implementations or wants to
    | trim the delivered JavaScript surface.
    */
    'load_alpine' => true,
];
