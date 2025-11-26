<?php

namespace Tests\Feature;

use App\Enums\OrgRole;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_organization_during_registration(): void
    {
        $response = $this->post('/register', [
            'mode' => 'create',
            'name' => 'Jane Founder',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'organization_name' => 'Test Org',
            'plan_tier' => 'free',
        ]);

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('organizations', [
            'name' => 'Test Org',
        ]);

        $this->assertAuthenticated();
        $this->assertNotNull(session('tenant_id'));
    }

    public function test_can_join_existing_organization(): void
    {
        $organization = Organization::factory()->create();

        $response = $this->post('/register', [
            'mode' => 'join',
            'name' => 'Alex Agent',
            'email' => 'alex@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'organization_id' => $organization->id,
            'role' => OrgRole::AGENT->value,
        ]);

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('organization_user', [
            'organization_id' => $organization->id,
            'user_id' => auth()->id(),
        ]);
    }
}
