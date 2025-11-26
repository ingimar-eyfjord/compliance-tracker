<?php

namespace Database\Factories;

use App\Models\CaseFile;
use App\Models\Organization;
use App\Models\ReporterPortalToken;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ReporterPortalToken>
 */
class ReporterPortalTokenFactory extends Factory
{
    protected $model = ReporterPortalToken::class;

    public function definition(): array
    {
        $selector = Str::random(20);
        $verifier = Str::random(40);

        return [
            'organization_id' => Organization::factory(),
            'case_id' => CaseFile::factory(),
            'selector' => $selector,
            'verifier_hash' => hash('sha256', $verifier),
            'last_seen_at' => now(),
            'expires_at' => now()->addMonths(6),
            'revoked_at' => null,
        ];
    }

    public function expired(): self
    {
        return $this->state(fn () => [
            'expires_at' => now()->subDay(),
        ]);
    }

    public function revoked(): self
    {
        return $this->state(fn () => [
            'revoked_at' => now(),
        ]);
    }
}
