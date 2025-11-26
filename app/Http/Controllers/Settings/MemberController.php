<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MemberController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $members = $organization->memberships()
            ->with('user:id,name,email')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($membership) => [
                'id' => sprintf('%s:%s', $membership->organization_id, $membership->user_id),
                'user' => [
                    'name' => $membership->user?->name,
                    'email' => $membership->user?->email,
                ],
                'role' => $membership->role->value,
                'status' => $membership->status->value,
            ]);

        return Inertia::render('settings/members/index', [
            'members' => $members,
        ]);
    }
}
