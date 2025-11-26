<?php

namespace Database\Factories;

use App\Enums\SenderType;
use App\Models\CaseFile;
use App\Models\CaseMessage;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CaseMessage>
 */
class CaseMessageFactory extends Factory
{
    protected $model = CaseMessage::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'case_id' => CaseFile::factory(),
            'sender_type' => SenderType::AGENT,
            'sender_user_id' => User::factory(),
            'ciphertext' => [
                'payload' => base64_encode(Str::random(120)),
            ],
            'attachments' => [],
            'sent_at' => now(),
        ];
    }

    public function reporter(): self
    {
        return $this->state(fn () => [
            'sender_type' => SenderType::REPORTER,
            'sender_user_id' => null,
        ]);
    }
}
