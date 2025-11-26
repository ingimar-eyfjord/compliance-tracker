<?php

namespace Database\Seeders;

use App\Enums\DeadlineType;
use App\Enums\MembershipStatus;
use App\Models\AuditLog;
use App\Models\BreachLog;
use App\Models\CaseEvent;
use App\Models\CaseFile;
use App\Models\CaseMessage;
use App\Models\Channel;
use App\Models\Deadline;
use App\Models\FeatureFlag;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Report;
use App\Models\ReporterPortalToken;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(15)->create();

        // Ensure we have a known login user for dev/testing
        $primaryUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        if (! $users->contains($primaryUser)) {
            $users->push($primaryUser);
        }

        $organizations = Organization::factory()
            ->count(3)
            ->create();

        $organizations->each(function (Organization $organization) use ($users, $primaryUser) {
            // Owner membership
            OrganizationMembership::factory()
                ->owner()
                ->create([
                    'organization_id' => $organization->id,
                    'user_id' => $organization->owner_user_id,
                    'status' => MembershipStatus::ACTIVE,
                    'accepted_at' => now(),
                ]);

            // Additional team members
            $team = $users->where('id', '!=', $organization->owner_user_id)->random(4);

            $team->each(function (User $user) use ($organization) {
                OrganizationMembership::factory()->create([
                    'organization_id' => $organization->id,
                    'user_id' => $user->id,
                ]);
            });

            // Feature flags
            FeatureFlag::factory()
                ->count(2)
                ->create(['organization_id' => $organization->id]);

            // Channels and nested data
            Channel::factory()
                ->count(2)
                ->create(['organization_id' => $organization->id])
                ->each(function (Channel $channel) use ($organization, $team, $primaryUser) {
                    Report::factory()
                        ->count(3)
                        ->create([
                            'organization_id' => $organization->id,
                            'channel_id' => $channel->id,
                        ])
                        ->each(function (Report $report) use ($organization, $team, $primaryUser) {
                            $assignee = $team->random() ?? $primaryUser;

                            $case = CaseFile::factory()->create([
                                'organization_id' => $organization->id,
                                'report_id' => $report->id,
                                'assignee_user_id' => $assignee->id,
                            ]);

                            // Messages
                            $messages = CaseMessage::factory()
                                ->count(2)
                                ->create([
                                    'organization_id' => $organization->id,
                                    'case_id' => $case->id,
                                    'sender_user_id' => $assignee->id,
                                ]);

                            CaseMessage::factory()
                                ->reporter()
                                ->create([
                                    'organization_id' => $organization->id,
                                    'case_id' => $case->id,
                                ]);

                            // Deadlines (ACK/Triage/Assign/Feedback)
                            collect([
                                DeadlineType::ACK,
                                DeadlineType::TRIAGE,
                                DeadlineType::ASSIGN,
                                DeadlineType::FEEDBACK,
                            ])->each(function ($type, $index) use ($organization, $case) {
                                Deadline::factory()->create([
                                    'organization_id' => $organization->id,
                                    'case_id' => $case->id,
                                    'type' => $type,
                                    'due_at' => now()->addDays(($index + 1) * 2),
                                ]);
                            });

                            ReporterPortalToken::factory()->create([
                                'organization_id' => $organization->id,
                                'case_id' => $case->id,
                            ]);

                            CaseEvent::factory()->create([
                                'organization_id' => $organization->id,
                                'case_id' => $case->id,
                                'actor_user_id' => $assignee->id,
                            ]);

                            AuditLog::factory()->create([
                                'organization_id' => $organization->id,
                                'user_id' => $assignee->id,
                                'entity_id' => $case->id,
                            ]);

                            // Attachments for first message
                            $messages->each(function (CaseMessage $message) use ($organization) {
                                Attachment::factory()->count(1)->create([
                                    'organization_id' => $organization->id,
                                    'attachable_type' => CaseMessage::class,
                                    'attachable_id' => $message->id,
                                ]);
                            });
                        });
                });

            $relatedCaseId = CaseFile::where('organization_id', $organization->id)->inRandomOrder()->value('id');

            if ($relatedCaseId) {
                BreachLog::factory()->count(1)->create([
                    'organization_id' => $organization->id,
                    'case_id' => $relatedCaseId,
                    'created_by' => $team->first()?->id ?? $organization->owner_user_id,
                ]);
            }
        });
    }
}
