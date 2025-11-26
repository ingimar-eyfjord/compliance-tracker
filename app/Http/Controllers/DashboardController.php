<?php

namespace App\Http\Controllers;

use App\Models\CaseFile;
use App\Models\Organization;
use App\Models\Report;
use App\Models\Channel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        /** @var Organization|null $organization */
        $organization = $request->attributes->get('tenant');

        if (! $organization) {
            return Inertia::render('dashboard', [
                'latestCase' => null,
                'allCases' => [],
                'allChannels' => [],
                'allReports' => [],
                'latestReport' => null,
            ]);
        }

        $allChannels = Channel::query()
            ->forOrganization($organization)
            ->get(['id', 'name', 'status']);

        $allReports = Report::query()
            ->forOrganization($organization)
            ->with('channel')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'channel_id', 'status']);

        $allCases = CaseFile::query()
            ->forOrganization($organization)
            ->with('assignee:id,name,email',)
            ->orderBy('created_at', 'desc')
            ->get();


        return Inertia::render('dashboard', [
            'allChannels' => $allChannels
                ->map(fn (Channel $channel) => [
                    'id' => $channel->id,
                    'name' => $channel->name,
                    'status' => $channel->status,
                ])
                ->values()
                ->all(),
            'allCases' => $allCases
                ->map(fn (CaseFile $case) => [
                    'id' => $case->id,
                    'report_id' => $case->report_id,
                    'priority' => $case->priority->value,
                    'status' => $case->status->value,
                    'tags' => $case->tags,
                    'created_at' => $case->created_at->toDateTimeString(),
                    'assignee' => $case->assignee ? [
                        'id' => $case->assignee->id,
                        'name' => $case->assignee->name,
                        'email' => $case->assignee->email,
                        ] : null,
                ])
                ->values()
                ->all(),
            'allReports' => $allReports
                ->map(fn (Report $report) => [
                    'id' => $report->id,
                    'channel_id' => $report->channel?->name,
                    'status' => $report->status->value,
                ])
                ->values()
                ->all()
        ]);
    }
}
