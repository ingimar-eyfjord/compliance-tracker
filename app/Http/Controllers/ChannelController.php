<?php

namespace App\Http\Controllers;

use App\Enums\AuditEvent;
use App\Models\Channel;
use App\Models\Organization;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ChannelController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $channels = Channel::query()
            ->forOrganization($organization)
            ->orderBy('created_at')
            ->get(['id', 'name', 'slug', 'status']);

        return Inertia::render('channels/index', [
            'channels' => $channels,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('channels/create');
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('channels', 'slug')->where('organization_id', $organization->id),
            ],
            'status' => ['nullable', new Enum(\App\Enums\ChannelStatus::class)],
            'intake_settings' => ['nullable', 'array'],
            'public_key' => ['nullable', 'string'],
        ]);

        $channel = Channel::create([
            'organization_id' => $organization->id,
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['name']).'-'.Str::lower(Str::random(4)),
            'status' => $validated['status'] ?? \App\Enums\ChannelStatus::ACTIVE,
            'intake_settings' => $validated['intake_settings'] ?? [],
            'public_key' => $validated['public_key'] ?? base64_encode(Str::random(64)),
        ]);

        AuditLogger::log($organization, AuditEvent::CREATED, $channel);

        return redirect()->route('channels.index')->with('success', 'Channel created.');
    }

    public function show(Request $request, Channel $channel): Response
    {
        $this->authorizeChannel($request, $channel);

        return Inertia::render('channels/show', [
            'channel' => $channel->only(['id', 'name', 'slug', 'status']),
        ]);
    }

    public function edit(Request $request, Channel $channel): Response
    {
        $this->authorizeChannel($request, $channel);

        return Inertia::render('channels/edit', [
            'channel' => $channel->only(['id', 'name', 'slug', 'status']),
        ]);
    }

    public function update(Request $request, Channel $channel): RedirectResponse
    {
        $this->authorizeChannel($request, $channel);

        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('channels', 'slug')
                    ->where('organization_id', $organization->id)
                    ->ignore($channel->id),
            ],
            'status' => ['sometimes', new Enum(\App\Enums\ChannelStatus::class)],
            'intake_settings' => ['nullable', 'array'],
        ]);

        $original = $channel->getOriginal();

        $channel->fill($validated);
        $channel->save();

        $diff = array_diff_assoc($channel->getAttributes(), $original);

        if (! empty($diff)) {
            AuditLogger::log($organization, AuditEvent::UPDATED, $channel, $diff);
        }

        return redirect()->route('channels.show', $channel)->with('success', 'Channel updated.');
    }

    public function destroy(Request $request, Channel $channel): RedirectResponse
    {
        $this->authorizeChannel($request, $channel);

        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        $channel->delete();

        AuditLogger::log($organization, AuditEvent::DELETED, $channel);

        return redirect()->route('channels.index')->with('success', 'Channel deleted.');
    }

    protected function authorizeChannel(Request $request, Channel $channel): void
    {
        /** @var Organization $organization */
        $organization = $request->attributes->get('tenant');

        if ($channel->organization_id !== $organization->getKey()) {
            throw ValidationException::withMessages([
                'channel' => 'Channel not found in this organization.',
            ]);
        }
    }
}
