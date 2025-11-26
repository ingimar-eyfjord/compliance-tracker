<?php

namespace App\Http\Controllers;

use App\Enums\AuditEvent;
use App\Models\BreachLog;
use App\Models\CaseFile;
use App\Models\Organization;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class BreachLogController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $breaches = BreachLog::query()
            ->forOrganization($organization)
            ->orderByDesc('detected_at')
            ->get(['id', 'description', 'detected_at', 'authority_notified']);

        return Inertia::render('breaches/index', [
            'breaches' => $breaches,
        ]);
    }

    public function create(Request $request): Response
    {
        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $cases = CaseFile::query()
            ->forOrganization($organization)
            ->orderBy('created_at')
            ->get(['id']);

        return Inertia::render('breaches/create', [
            'cases' => $cases,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $validated = $request->validate([
            'case_id' => [
                'nullable',
                Rule::exists('cases', 'id')->where('organization_id', $organization->id),
            ],
            'description' => ['required', 'string'],
            'authority_notified' => ['boolean'],
        ]);

        $breach = BreachLog::create([
            'organization_id' => $organization->id,
            'case_id' => $validated['case_id'] ?? null,
            'detected_at' => now(),
            'description' => $validated['description'],
            'impact' => [],
            'remediation' => [],
            'authority_notified' => $validated['authority_notified'] ?? false,
            'created_by' => $request->user()->id,
        ]);

        AuditLogger::log($organization, AuditEvent::CREATED, $breach);

        return redirect()->route('breaches.show', $breach)->with('success', 'Breach recorded.');
    }

    public function show(Request $request, BreachLog $breach): Response
    {
        $this->authorizeBreach($request, $breach);

        return Inertia::render('breaches/show', [
            'breach' => $breach->only(['id', 'description', 'detected_at', 'authority_notified']),
        ]);
    }

    public function edit(Request $request, BreachLog $breach): Response
    {
        $this->authorizeBreach($request, $breach);

        return Inertia::render('breaches/edit', [
            'breach' => $breach->only(['id', 'description', 'authority_notified']),
        ]);
    }

    public function update(Request $request, BreachLog $breach): RedirectResponse
    {
        $this->authorizeBreach($request, $breach);

        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $validated = $request->validate([
            'description' => ['sometimes', 'string'],
            'authority_notified' => ['sometimes', 'boolean'],
        ]);

        $original = $breach->getOriginal();
        $breach->fill($validated);
        $breach->save();

        $diff = array_diff_assoc($breach->getAttributes(), $original);

        if (! empty($diff)) {
            AuditLogger::log($organization, AuditEvent::UPDATED, $breach, $diff);
        }

        return redirect()->route('breaches.show', $breach)->with('success', 'Breach updated.');
    }

    public function destroy(Request $request, BreachLog $breach): RedirectResponse
    {
        $this->authorizeBreach($request, $breach);

        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $breach->delete();

        AuditLogger::log($organization, AuditEvent::DELETED, $breach);

        return redirect()->route('breaches.index')->with('success', 'Breach removed.');
    }

    protected function authorizeBreach(Request $request, BreachLog $breach): void
    {
        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        if ($breach->organization_id !== $organization->getKey()) {
            throw ValidationException::withMessages([
                'breach' => 'Breach not found in this organization.',
            ]);
        }
    }
}
