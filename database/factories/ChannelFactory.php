<?php

namespace Database\Factories;

use App\Enums\ChannelStatus;
use App\Models\Channel;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Channel>
 */
class ChannelFactory extends Factory
{
    protected $model = Channel::class;

    public function definition(): array
    {
        $name = $this->faker->sentence(3);

        return [
            'organization_id' => Organization::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'status' => ChannelStatus::ACTIVE,
            'intake_settings' => [
                'captcha' => true,
                'languages' => [$this->faker->locale()],
            ],
            'public_key' => base64_encode(Str::random(64)),
        ];
    }

    public function archived(): self
    {
        return $this->state(fn () => ['status' => ChannelStatus::ARCHIVED]);
    }
}
