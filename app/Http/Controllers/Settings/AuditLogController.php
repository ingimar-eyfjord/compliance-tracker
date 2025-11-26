<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuditLogController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $logs = AuditLog::query()
            ->where('organization_id', $organization->id)
            ->latest('created_at')
            ->limit(25)
            ->get(['id', 'event', 'entity_type', 'entity_id', 'created_at']);

        return Inertia::render('settings/audit/index', [
            'logs' => $logs,
        ]);
    }
}
