<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFeatureEnabled
{
    /**
     * Ensure the feature is enabled for the tenant.
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        /** @var Organization|null $organization */
        $organization = $request->attributes->get('tenant');

        abort_if(! $organization, 400, 'Organization context is missing.');

        $manifest = $organization->feature_manifest ?? [];
        $enabled = (bool) ($manifest[$feature] ?? false);

        if (! $enabled) {
            $enabled = $organization->featureFlags()
                ->where('feature_key', $feature)
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->exists();
        }

        abort_unless($enabled, 403, 'Feature not enabled for this organization.');

        return $next($request);
    }
}
