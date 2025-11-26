<?php

namespace Database\Factories;

use App\Enums\PlanTier;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'plan_tier' => PlanTier::FREE,
            'feature_manifest' => [],
            'domain' => null,
            'owner_user_id' => User::factory(),
            'settings' => [],
        ];
    }

    public function pro(): self
    {
        return $this->state(fn () => ['plan_tier' => PlanTier::PRO]);
    }

    public function enterprise(): self
    {
        return $this->state(fn () => ['plan_tier' => PlanTier::ENTERPRISE]);
    }
}
