<?php

namespace App\Models;

use App\Enums\CasePriority;
use App\Enums\CaseStatus;
use App\Models\Concerns\HasUuid;
use App\Models\Concerns\ScopedByOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CaseFile extends Model
{
    use HasFactory;
    use HasUuid;
    use ScopedByOrganization;

    protected $table = 'cases';

    protected $guarded = [];

    protected $casts = [
        'status' => CaseStatus::class,
        'priority' => CasePriority::class,
        'due_at' => 'datetime',
        'tags' => 'array',
        'properties' => 'array',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(CaseMessage::class, 'case_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(CaseEvent::class, 'case_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function deadlines(): HasMany
    {
        return $this->hasMany(Deadline::class, 'case_id');
    }

    public function reporterTokens(): HasMany
    {
        return $this->hasMany(ReporterPortalToken::class, 'case_id');
    }
}
