<?php

namespace Database\Factories;

use App\Enums\IngestChannel;
use App\Enums\ReportStatus;
use App\Models\Channel;
use App\Models\Organization;
use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'channel_id' => Channel::factory(),
            'status' => ReportStatus::NEW,
            'ciphertext' => [
                'version' => 'v1',
                'payload' => base64_encode(Str::random(120)),
            ],
            'metadata' => [
                'locale' => $this->faker->locale(),
                'ip' => $this->faker->ipv4(),
            ],
            'created_via' => IngestChannel::WEB,
            'reference_code' => strtoupper(Str::random(10)),
            'received_at' => now(),
            'acknowledged_at' => null,
        ];
    }

    public function acknowledged(): self
    {
        return $this->state(fn () => [
            'status' => ReportStatus::TRIAGED,
            'acknowledged_at' => now(),
        ]);
    }
}
