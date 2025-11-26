<?php

namespace App\Http\Controllers;

use App\Enums\AuditEvent;
use App\Enums\CasePriority;
use App\Enums\CaseStatus;
use App\Models\CaseFile;
use App\Models\Organization;
use App\Models\Report;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CaseController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $filters = [
            'filter' => trim((string) $request->input('filter', '')),
            'status' => array_values(array_filter((array) $request->input('status', []))),
            'priority' => array_values(array_filter((array) $request->input('priority', []))),
            'page' => (int) $request->input('page', 1),
            'pageSize' => (int) $request->input('pageSize', 10),
        ];

        $query = CaseFile::query()
            ->forOrganization($organization)
            ->with(['assignee:id,name', 'report:id,reference_code'])
            ->orderByDesc('created_at');

        if ($filters['filter'] !== '') {
            $search = mb_strtolower($filters['filter']);
            $query->where(function ($builder) use ($search) {
                $builder->whereRaw('LOWER(id) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(priority) LIKE ?', ["%{$search}%"])
                    ->orWhereHas('report', function ($reportQuery) use ($search) {
                        $reportQuery->whereRaw('LOWER(reference_code) LIKE ?', ["%{$search}%"]);
                    })
                    ->orWhereHas('assignee', function ($assigneeQuery) use ($search) {
                        $assigneeQuery->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                    });
            });
        }

        if (! empty($filters['status'])) {
            $statuses = collect($filters['status'])
                ->map(fn (string $value) => CaseStatus::tryFrom($value))
                ->filter()
                ->map(fn (CaseStatus $status) => $status->value)
                ->all();

            if (! empty($statuses)) {
                $query->whereIn('status', $statuses);
            }
        }

        if (! empty($filters['priority'])) {
            $priorities = collect($filters['priority'])
                ->map(fn (string $value) => CasePriority::tryFrom($value))
                ->filter()
                ->map(fn (CasePriority $priority) => $priority->value)
                ->all();

            if (! empty($priorities)) {
                $query->whereIn('priority', $priorities);
            }
        }

        $cases = $query->get();

        return Inertia::render('cases/index', [
            'cases' => $cases->map(fn (CaseFile $case) => [
                'id' => $case->id,
                'status' => $case->status->value,
                'priority' => $case->priority->value,
                'assignee' => $case->assignee?->name,
                'reference' => $case->report?->reference_code,
            ]),
            'filters' => $filters,
        ]);
    }

    public function create(Request $request): Response
    {
        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $reports = Report::query()
            ->forOrganization($organization)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['id', 'reference_code']);

        return Inertia::render('cases/create', [
            'reports' => $reports,
            'statuses' => collect(CaseStatus::cases())->map(fn ($status) => $status->value),
            'priorities' => collect(CasePriority::cases())->map(fn ($priority) => $priority->value),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $statusValues = array_map(fn (CaseStatus $status) => $status->value, CaseStatus::cases());
        $priorityValues = array_map(fn (CasePriority $priority) => $priority->value, CasePriority::cases());

        $validated = $request->validate([
            'report_id' => [
                'required',
                Rule::exists('reports', 'id')->where('organization_id', $organization->id),
            ],
            'assignee_user_id' => [
                'nullable',
                Rule::exists('users', 'id'),
            ],
            'status' => ['required', Rule::in($statusValues)],
            'priority' => ['required', Rule::in($priorityValues)],
            'due_at' => ['nullable', 'date'],
        ]);

        $case = CaseFile::create([
            'organization_id' => $organization->id,
            'report_id' => $validated['report_id'],
            'assignee_user_id' => $validated['assignee_user_id'] ?? null,
            'status' => CaseStatus::from($validated['status']),
            'priority' => CasePriority::from($validated['priority']),
            'due_at' => $validated['due_at'] ?? null,
        ]);

        AuditLogger::log($organization, AuditEvent::CREATED, $case);

        return redirect()->route('cases.show', $case)->with('success', 'Case created.');
    }

    public function show(Request $request, CaseFile $case): Response
    {
        $this->authorizeCase($request, $case);

        $case->load(['assignee:id,name', 'report:id,reference_code']);

        return Inertia::render('cases/show', [
            'caseItem' => [
                'id' => $case->id,
                'status' => $case->status->value,
                'priority' => $case->priority->value,
                'assignee' => $case->assignee?->name,
                'reference' => $case->report?->reference_code,
            ],
        ]);
    }

    public function edit(Request $request, CaseFile $case): Response
    {
        $this->authorizeCase($request, $case);

        return Inertia::render('cases/edit', [
            'caseItem' => [
                'id' => $case->id,
                'status' => $case->status->value,
                'priority' => $case->priority->value,
                'assignee_user_id' => $case->assignee_user_id,
                'due_at' => $case->due_at?->toIso8601String(),
            ],
            'statuses' => collect(CaseStatus::cases())->map(fn ($status) => $status->value),
            'priorities' => collect(CasePriority::cases())->map(fn ($priority) => $priority->value),
        ]);
    }

    public function update(Request $request, CaseFile $case): RedirectResponse
    {
        $this->authorizeCase($request, $case);

        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $statusValues = array_map(fn (CaseStatus $status) => $status->value, CaseStatus::cases());
        $priorityValues = array_map(fn (CasePriority $priority) => $priority->value, CasePriority::cases());

        $validated = $request->validate([
            'assignee_user_id' => [
                'nullable',
                Rule::exists('users', 'id'),
            ],
            'status' => ['nullable', Rule::in($statusValues)],
            'priority' => ['nullable', Rule::in($priorityValues)],
            'due_at' => ['nullable', 'date'],
        ]);

        $original = $case->getOriginal();

        if (isset($validated['status'])) {
            $validated['status'] = CaseStatus::from($validated['status']);
        }

        if (isset($validated['priority'])) {
            $validated['priority'] = CasePriority::from($validated['priority']);
        }

        $case->fill($validated);
        $case->save();

        $diff = array_diff_assoc($case->getAttributes(), $original);

        if (! empty($diff)) {
            AuditLogger::log($organization, AuditEvent::UPDATED, $case, $diff);
        }

        return redirect()->route('cases.show', $case)->with('success', 'Case updated.');
    }

    public function destroy(Request $request, CaseFile $case): RedirectResponse
    {
        $this->authorizeCase($request, $case);

        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $case->delete();

        AuditLogger::log($organization, AuditEvent::DELETED, $case);

        return redirect()->route('cases.index')->with('success', 'Case deleted.');
    }

    protected function authorizeCase(Request $request, CaseFile $case): void
    {
        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        if ($case->organization_id !== $organization->getKey()) {
            throw ValidationException::withMessages([
                'case' => 'Case not found in this organization.',
            ]);
        }
    }
}
