<?php

namespace Database\Factories;

use App\Enums\MembershipStatus;
use App\Enums\OrgRole;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrganizationMembership>
 */
class OrganizationMembershipFactory extends Factory
{
    protected $model = OrganizationMembership::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'user_id' => User::factory(),
            'role' => $this->faker->randomElement(OrgRole::cases()),
            'status' => MembershipStatus::ACTIVE,
            'invited_by' => null,
            'invited_at' => null,
            'accepted_at' => now(),
        ];
    }

    public function invited(): self
    {
        return $this->state(fn () => [
            'status' => MembershipStatus::INVITED,
            'accepted_at' => null,
        ]);
    }

    public function owner(): self
    {
        return $this->state(fn () => [
            'role' => OrgRole::OWNER,
        ]);
    }
}
