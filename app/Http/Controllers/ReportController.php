<?php

namespace App\Http\Controllers;

use App\Enums\AuditEvent;
use App\Enums\IngestChannel;
use App\Enums\ReportStatus;
use App\Models\Channel;
use App\Models\Organization;
use App\Models\Report;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $reports = Report::query()
            ->forOrganization($organization)
            ->with('channel:id,name')
            ->orderBy('created_at')
            ->get(['id', 'channel_id', 'reference_code', 'created_via']);

        return Inertia::render('reports/index', [
            'reports' => $reports->map(fn (Report $report) => [
                'id' => $report->id,
                'reference_code' => $report->reference_code,
                'channel' => $report->channel?->name,
                'created_via' => $report->created_via->value,
            ]),
        ]);
    }

    public function create(Request $request): Response
    {
        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $channels = Channel::query()
            ->forOrganization($organization)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('reports/create', [
            'channels' => $channels,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $ingestValues = array_map(fn (IngestChannel $channel) => $channel->value, IngestChannel::cases());

        $validated = $request->validate([
            'channel_id' => [
                'required',
                Rule::exists('channels', 'id')->where('organization_id', $organization->id),
            ],
            'created_via' => ['nullable', Rule::in($ingestValues)],
            'metadata' => ['nullable', 'array'],
        ]);

        $report = Report::create([
            'organization_id' => $organization->id,
            'channel_id' => $validated['channel_id'],
            'status' => ReportStatus::NEW,
            'ciphertext' => [
                'payload' => base64_encode(Str::random(120)),
            ],
            'metadata' => $validated['metadata'] ?? [],
            'created_via' => isset($validated['created_via'])
                ? IngestChannel::from($validated['created_via'])
                : IngestChannel::WEB,
            'reference_code' => strtoupper(Str::random(10)),
            'received_at' => now(),
        ]);

        AuditLogger::log($organization, AuditEvent::CREATED, $report);

        return redirect()->route('reports.show', $report)->with('success', 'Report created.');
    }

    public function show(Request $request, Report $report): Response
    {
        $this->authorizeReport($request, $report);

        $report->load('channel:id,name');

        return Inertia::render('reports/show', [
            'report' => [
                'id' => $report->id,
                'reference_code' => $report->reference_code,
                'channel' => $report->channel?->name,
                'created_via' => $report->created_via->value,
            ],
        ]);
    }

    public function edit(Request $request, Report $report): Response
    {
        $this->authorizeReport($request, $report);

        return Inertia::render('reports/edit', [
            'report' => [
                'id' => $report->id,
                'status' => $report->status->value,
            ],
        ]);
    }

    public function update(Request $request, Report $report): RedirectResponse
    {
        $this->authorizeReport($request, $report);

        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $statusValues = array_map(fn (ReportStatus $status) => $status->value, ReportStatus::cases());

        $validated = $request->validate([
            'status' => ['nullable', Rule::in($statusValues)],
            'metadata' => ['nullable', 'array'],
        ]);

        $original = $report->getOriginal();

        if (isset($validated['status'])) {
            $validated['status'] = ReportStatus::from($validated['status']);
        }

        $report->fill($validated);
        $report->save();

        $diff = array_diff_assoc($report->getAttributes(), $original);

        if (! empty($diff)) {
            AuditLogger::log($organization, AuditEvent::UPDATED, $report, $diff);
        }

        return redirect()->route('reports.show', $report)->with('success', 'Report updated.');
    }

    public function destroy(Request $request, Report $report): RedirectResponse
    {
        $this->authorizeReport($request, $report);

        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $report->delete();

        AuditLogger::log($organization, AuditEvent::DELETED, $report);

        return redirect()->route('reports.index')->with('success', 'Report deleted.');
    }

    protected function authorizeReport(Request $request, Report $report): void
    {
        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        if ($report->organization_id !== $organization->getKey()) {
            throw ValidationException::withMessages([
                'report' => 'Report not found in this organization.',
            ]);
        }
    }
}
