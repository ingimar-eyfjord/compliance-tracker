<?php

namespace Database\Factories;

use App\Enums\ActorType;
use App\Enums\AuditEvent;
use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'user_id' => User::factory(),
            'actor_type' => ActorType::USER,
            'actor_id' => null,
            'event' => AuditEvent::CREATED,
            'entity_type' => \App\Models\CaseFile::class,
            'entity_id' => Str::uuid()->toString(),
            'diff' => ['before' => [], 'after' => []],
            'ip' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'created_at' => now(),
        ];
    }
}
