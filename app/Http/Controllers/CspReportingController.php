<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CspViolation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class CspReportingController
{
    public function __invoke(Request $request): Response
    {
        /** @var array<string, mixed>|null $report */
        $report = $request->input('csp-report');

        if (is_array($report)) {
            CspViolation::query()->create([
                'blocked_uri' => $report['blocked-uri'] ?? null,
                'document_uri' => $report['document-uri'] ?? null,
                'violated_directive' => $report['violated-directive'] ?? null,
                'original_policy' => $report['original-policy'] ?? null,
                'ip_address' => $request->ip(),
            ]);
        }

        return response()->noContent();
    }
}
