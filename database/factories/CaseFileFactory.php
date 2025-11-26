<?php

namespace Database\Factories;

use App\Enums\CasePriority;
use App\Enums\CaseStatus;
use App\Models\CaseFile;
use App\Models\Organization;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CaseFile>
 */
class CaseFileFactory extends Factory
{
    protected $model = CaseFile::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'report_id' => Report::factory(),
            'assignee_user_id' => User::factory(),
            'status' => CaseStatus::NEW,
            'priority' => CasePriority::MEDIUM,
            'due_at' => now()->addDays(14),
            'tags' => [],
            'properties' => [],
        ];
    }

    public function unresolved(): self
    {
        return $this->state(fn () => [
            'status' => CaseStatus::IN_PROGRESS,
        ]);
    }

    public function resolved(): self
    {
        return $this->state(fn () => [
            'status' => CaseStatus::RESOLVED,
            'due_at' => null,
        ]);
    }
}
