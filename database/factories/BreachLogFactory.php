<?php

namespace Database\Factories;

use App\Models\BreachLog;
use App\Models\CaseFile;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BreachLog>
 */
class BreachLogFactory extends Factory
{
    protected $model = BreachLog::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'case_id' => CaseFile::factory(),
            'detected_at' => now()->subHours(3),
            'notified_at' => null,
            'authority_notified' => false,
            'description' => $this->faker->sentence(),
            'impact' => ['records' => $this->faker->numberBetween(5, 50)],
            'remediation' => ['actions' => ['rotate_keys', 'notify_team']],
            'created_by' => User::factory(),
        ];
    }

    public function notified(): self
    {
        return $this->state(fn () => [
            'notified_at' => now(),
            'authority_notified' => true,
        ]);
    }
}
