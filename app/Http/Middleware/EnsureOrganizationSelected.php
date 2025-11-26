<?php

namespace App\Http\Middleware;

use App\Enums\MembershipStatus;
use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $tenantId = $request->session()->get('tenant_id');

        $membershipsQuery = $request->user()
            ->memberships()
            ->with('organization')
            ->where('status', MembershipStatus::ACTIVE);

        $membershipCount = (clone $membershipsQuery)->count();

        if ($membershipCount === 0) {
            return $next($request);
        }

        if (! $tenantId && $membershipCount === 1) {
            $membership = $membershipsQuery->first();
            if ($membership && $membership->organization) {
                $request->session()->put('tenant_id', $membership->organization_id);
                $tenantId = $membership->organization_id;
            }
        }

        if (! $tenantId) {
            if ($this->isOrganizationSelectionRoute($request)) {
                return $next($request);
            }

            return redirect()->route('orgs.select');
        }

        $membership = $membershipsQuery
            ->where('organization_id', $tenantId)
            ->first();

        if (! $membership || ! $membership->organization) {
            $request->session()->forget('tenant_id');

            if ($this->isOrganizationSelectionRoute($request)) {
                return $next($request);
            }

            return redirect()->route('orgs.select');
        }

        /** @var Organization $organization */
        $organization = $membership->organization;

        $request->attributes->set('tenant', $organization);
        $request->attributes->set('tenant_membership', $membership);

        return $next($request);
    }

    protected function isOrganizationSelectionRoute(Request $request): bool
    {
        return $request->routeIs('orgs.select', 'orgs.switch');
    }
}
