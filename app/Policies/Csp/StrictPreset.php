<?php

declare(strict_types=1);

namespace App\Policies\Csp;

use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;
use Spatie\Csp\Policy;
use Spatie\Csp\Preset;
use Spatie\Csp\Presets\GoogleAnalytics;
use Spatie\Csp\Presets\Stripe;

final class StrictPreset implements Preset
{
    public function configure(Policy $policy): void
    {
        $policy
            ->add(Directive::BASE, Keyword::SELF)
            ->add(Directive::CONNECT, Keyword::SELF)
            ->add(Directive::DEFAULT, Keyword::SELF)
            ->add(Directive::FORM_ACTION, Keyword::SELF)
            ->add(Directive::IMG, [Keyword::SELF, 'data:'])
            ->add(Directive::MEDIA, Keyword::SELF)
            ->add(Directive::OBJECT, Keyword::NONE)
            ->add(Directive::STYLE, [
                Keyword::SELF,
                Keyword::UNSAFE_INLINE, // Necessary for many JS libraries that inject styles
            ])
            ->addNonce(Directive::STYLE)

            // THE CRITICAL PART: Script Handling
            ->add(Directive::SCRIPT, [
                Keyword::SELF,
                'strict-dynamic',   // Trust scripts loaded by trusted scripts
                Keyword::UNSAFE_INLINE, // Fallback for older browsers (ignored by modern ones if nonce is present)
            ])
            ->addNonce(Directive::SCRIPT);

        // Configure Stripe and GoogleAnalytics presets
        new Stripe()->configure($policy);
        new GoogleAnalytics()->configure($policy);

        // Optional: Allow Hot Module Replacement (HMR) for local dev
        if (app()->environment('local')) {
            $policy->add(Directive::CONNECT, 'ws://localhost:5173');
        }
    }
}
