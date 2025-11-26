<?php

namespace App\Support;

use App\Enums\ActorType;
use App\Enums\AuditEvent;
use App\Models\AuditLog;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    /**
     * Record an audit log entry.
     */
    public static function log(
        Organization $organization,
        AuditEvent $event,
        Model $entity,
        array $diff = [],
        ?ActorType $actorType = null,
        ?Request $request = null,
    ): void {
        $request ??= request();
        $user = Auth::user();

        AuditLog::create([
            'organization_id' => $organization->getKey(),
            'user_id' => $user?->getKey(),
            'actor_type' => $actorType ?? ActorType::USER,
            'event' => $event,
            'entity_type' => $entity->getMorphClass(),
            'entity_id' => $entity->getKey(),
            'diff' => $diff,
            'ip' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'created_at' => now(),
        ]);
    }
}
