<?php

namespace Database\Factories;

use App\Models\FeatureFlag;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FeatureFlag>
 */
class FeatureFlagFactory extends Factory
{
    protected $model = FeatureFlag::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'feature_key' => $this->faker->unique()->slug(),
            'enabled_at' => now(),
            'expires_at' => null,
            'meta' => [],
        ];
    }

    public function trial(int $days = 14): self
    {
        return $this->state(fn () => [
            'expires_at' => now()->addDays($days),
        ]);
    }
}
