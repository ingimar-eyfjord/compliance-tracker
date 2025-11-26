<?php

namespace App\Http\Middleware;

use App\Enums\MembershipStatus;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        $user = $request->user();
        $tenant = $request->attributes->get('tenant');
        $tenantMembership = $request->attributes->get('tenant_membership');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ] : null,
                'tenant' => $tenant ? [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'slug' => $tenant->slug,
                    'plan_tier' => $tenant->plan_tier->value,
                    'feature_manifest' => $tenant->feature_manifest,
                    'role' => $tenantMembership?->role?->value,
                    'role_label' => $tenantMembership?->role?->label(),
                ] : null,
                'memberships' => $user ? $user->memberships()
                    ->with('organization')
                    ->where('status', MembershipStatus::ACTIVE)
                    ->get()
                    ->map(fn ($membership) => [
                        'organization' => [
                            'id' => $membership->organization_id,
                            'name' => $membership->organization?->name,
                            'slug' => $membership->organization?->slug,
                        ],
                        'role' => $membership->role->value,
                        'role_label' => $membership->role->label(),
                    ])
                    ->values()
                    : [],
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
