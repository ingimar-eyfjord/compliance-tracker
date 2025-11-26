<?php

namespace App\Models;

use App\Enums\MembershipStatus;
use App\Enums\OrgRole;
use App\Models\Concerns\ScopedByOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrganizationMembership extends Pivot
{
    use HasFactory;
    use ScopedByOrganization;

    protected $table = 'organization_user';

    public $incrementing = false;

    public $timestamps = true;

    protected $guarded = [];

    protected $casts = [
        'role' => OrgRole::class,
        'status' => MembershipStatus::class,
        'invited_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
