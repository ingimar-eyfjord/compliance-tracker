<?php

namespace App\Http\Controllers;

use App\Models\BreachLog;
use App\Models\CaseFile;
use App\Models\Deadline;
use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ComplianceOverviewController extends Controller
{
    public function __invoke(Request $request): Response
    {
        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $openCases = CaseFile::query()
            ->forOrganization($organization)
            ->whereNotIn('status', [\App\Enums\CaseStatus::RESOLVED, \App\Enums\CaseStatus::CLOSED])
            ->count();

        $breaches = BreachLog::query()
            ->forOrganization($organization)
            ->count();

        $deadlines = Deadline::query()
            ->forOrganization($organization)
            ->selectRaw("type, status, count(*) as total")
            ->groupBy('type', 'status')
            ->get();

        return Inertia::render('compliance/overview', [
            'summary' => [
                'open_cases' => $openCases,
                'breaches' => $breaches,
            ],
            'deadlines' => $deadlines,
        ]);
    }
}
