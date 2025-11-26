<?php

namespace Database\Factories;

use App\Enums\CaseEventType;
use App\Enums\CaseStatus;
use App\Models\CaseEvent;
use App\Models\CaseFile;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CaseEvent>
 */
class CaseEventFactory extends Factory
{
    protected $model = CaseEvent::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'case_id' => CaseFile::factory(),
            'event' => CaseEventType::STATUS_CHANGED,
            'actor_user_id' => User::factory(),
            'data' => [
                'from' => CaseStatus::NEW,
                'to' => CaseStatus::TRIAGE,
            ],
            'created_at' => now(),
        ];
    }
}
