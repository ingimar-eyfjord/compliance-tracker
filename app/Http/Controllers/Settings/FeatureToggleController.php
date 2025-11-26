<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\FeatureFlag;
use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeatureToggleController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $flags = FeatureFlag::query()
            ->where('organization_id', $organization->id)
            ->orderBy('feature_key')
            ->get(['id', 'feature_key', 'enabled_at', 'expires_at']);

        $manifest = collect($organization->feature_manifest ?? [])
            ->map(fn ($enabled, $key) => [
                'feature_key' => $key,
                'enabled' => (bool) $enabled,
                'source' => 'manifest',
            ])
            ->values();

        $featureFlags = $flags->map(fn (FeatureFlag $flag) => [
            'feature_key' => $flag->feature_key,
            'enabled' => true,
            'source' => 'flag',
            'enabled_at' => $flag->enabled_at?->toIso8601String(),
            'expires_at' => $flag->expires_at?->toIso8601String(),
        ]);

        $features = $manifest
            ->merge($featureFlags)
            ->groupBy('feature_key')
            ->map(fn ($items) => $items->values())
            ->toArray();

        return Inertia::render('settings/features/index', [
            'features' => $features,
        ]);
    }
}
