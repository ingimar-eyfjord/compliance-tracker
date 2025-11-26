<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\CaseMessage;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Attachment>
 */
class AttachmentFactory extends Factory
{
    protected $model = Attachment::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'attachable_type' => CaseMessage::class,
            'attachable_id' => CaseMessage::factory(),
            'disk' => 's3',
            'path' => 'attachments/' . Str::uuid() . '.bin',
            'original_name' => $this->faker->lexify('document-????') . '.pdf',
            'mime' => 'application/pdf',
            'size' => $this->faker->numberBetween(10_000, 500_000),
            'checksum' => Str::random(64),
            'encrypted_meta' => ['iv' => Str::random(24)],
        ];
    }
}
