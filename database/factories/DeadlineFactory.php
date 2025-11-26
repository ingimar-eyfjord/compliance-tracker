<?php

namespace Database\Factories;

use App\Enums\DeadlineStatus;
use App\Enums\DeadlineType;
use App\Models\CaseFile;
use App\Models\Deadline;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Deadline>
 */
class DeadlineFactory extends Factory
{
    protected $model = Deadline::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'case_id' => CaseFile::factory(),
            'type' => DeadlineType::ACK,
            'status' => DeadlineStatus::OPEN,
            'due_at' => now()->addDays(7),
            'met_at' => null,
            'metadata' => [],
        ];
    }

    public function met(): self
    {
        return $this->state(fn () => [
            'status' => DeadlineStatus::MET,
            'met_at' => now(),
        ]);
    }

    public function breached(): self
    {
        return $this->state(fn () => [
            'status' => DeadlineStatus::BREACHED,
            'due_at' => now()->subDay(),
        ]);
    }
}
