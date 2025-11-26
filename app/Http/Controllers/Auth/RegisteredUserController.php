<?php

namespace App\Http\Controllers\Auth;

use App\Enums\MembershipStatus;
use App\Enums\OrgRole;
use App\Enums\PlanTier;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        $organizations = Organization::query()
            ->select('id', 'name', 'slug')
            ->orderBy('name')
            ->get()
            ->map(fn (Organization $organization) => [
                'id' => $organization->id,
                'name' => $organization->name,
                'slug' => $organization->slug,
            ])
            ->values();

        $roles = collect(OrgRole::cases())
            ->map(fn (OrgRole $role) => [
                'value' => $role->value,
                'label' => $role->label(),
            ])
            ->values();

        $planTiers = collect(PlanTier::cases())
            ->map(fn (PlanTier $tier) => [
                'value' => $tier->value,
                'label' => $tier->label(),
            ])
            ->values();

        return Inertia::render('auth/register', [
            'organizations' => $organizations,
            'roles' => $roles,
            'planTiers' => $planTiers,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $roleValues = implode(',', collect(OrgRole::cases())->map->value->all());
        $planValues = implode(',', collect(PlanTier::cases())->map->value->all());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'mode' => 'nullable|in:create,join',
            'organization_name' => 'nullable|string|max:255',
            'plan_tier' => 'nullable|string|in:'.$planValues,
            'organization_id' => 'nullable|uuid|exists:organizations,id',
            'role' => 'nullable|string|in:'.$roleValues,
        ]);

        $mode = $validated['mode'] ?? 'create';

        if ($mode === 'join' && (! ($validated['organization_id'] ?? false) || ! ($validated['role'] ?? false))) {
            throw ValidationException::withMessages([
                'organization_id' => 'Please choose an organization to join.',
            ]);
        }

        $user = null;
        $organization = null;

        DB::transaction(function () use ($validated, $mode, &$user, &$organization) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            if ($mode === 'create') {
                $organizationName = $validated['organization_name']
                    ?? sprintf("%s's Organization", $validated['name']);

                $planTier = PlanTier::tryFrom($validated['plan_tier'] ?? PlanTier::FREE->value) ?? PlanTier::FREE;
                $slug = Str::slug($organizationName);
                $slug = $slug ? $slug.'-'.Str::lower(Str::random(4)) : Str::lower(Str::random(8));

                $organization = Organization::create([
                    'name' => $organizationName,
                    'slug' => $slug,
                    'plan_tier' => $planTier,
                    'feature_manifest' => [],
                    'domain' => null,
                    'owner_user_id' => $user->id,
                    'settings' => [],
                ]);

                OrganizationMembership::create([
                    'organization_id' => $organization->id,
                    'user_id' => $user->id,
                    'role' => OrgRole::OWNER,
                    'status' => MembershipStatus::ACTIVE,
                    'accepted_at' => now(),
                ]);
            } else {
                $organization = Organization::findOrFail($validated['organization_id']);

                OrganizationMembership::create([
                    'organization_id' => $organization->id,
                    'user_id' => $user->id,
                    'role' => OrgRole::from($validated['role']),
                    'status' => MembershipStatus::ACTIVE,
                    'accepted_at' => now(),
                ]);
            }
        });

        event(new Registered($user));

        Auth::login($user);

        $request->session()->regenerate();

        if (! $organization instanceof Organization) {
            abort(500, 'Unable to resolve organization membership during registration.');
        }

        if ($organization) {
            $request->session()->put('tenant_id', $organization->id);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
