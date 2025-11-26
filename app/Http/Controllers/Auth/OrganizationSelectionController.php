<?php

namespace App\Http\Controllers\Auth;

use App\Enums\MembershipStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationSelectionController extends Controller
{
    public function index(Request $request): Response
    {
        $memberships = $request->user()
            ->memberships()
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
            ->values();

        return Inertia::render('auth/select-organization', [
            'memberships' => $memberships,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'organization_id' => ['required', 'uuid'],
        ]);

        $membership = $request->user()
            ->memberships()
            ->where('organization_id', $validated['organization_id'])
            ->where('status', MembershipStatus::ACTIVE)
            ->first();

        abort_if(! $membership, 403);

        $request->session()->put('tenant_id', $membership->organization_id);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
