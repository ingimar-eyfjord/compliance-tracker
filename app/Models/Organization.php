<?php

namespace App\Models;

use App\Enums\PlanTier;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;
    use HasUuid;

    protected $guarded = [];

    protected $casts = [
        'feature_manifest' => 'array',
        'settings' => 'array',
        'plan_tier' => PlanTier::class,
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(OrganizationMembership::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_user')
            ->using(OrganizationMembership::class)
            ->withPivot([
                'role',
                'status',
                'invited_by',
                'invited_at',
                'accepted_at',
            ])
            ->withTimestamps();
    }

    public function featureFlags(): HasMany
    {
        return $this->hasMany(FeatureFlag::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function channels(): HasMany
    {
        return $this->hasMany(Channel::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function cases(): HasMany
    {
        return $this->hasMany(CaseFile::class, 'organization_id');
    }

    public function deadlines(): HasMany
    {
        return $this->hasMany(Deadline::class);
    }

    public function breachLogs(): HasMany
    {
        return $this->hasMany(BreachLog::class);
    }
}
