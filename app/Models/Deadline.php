<?php

namespace App\Models;

use App\Enums\DeadlineStatus;
use App\Enums\DeadlineType;
use App\Models\Concerns\HasUuid;
use App\Models\Concerns\ScopedByOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deadline extends Model
{
    use HasFactory;
    use HasUuid;
    use ScopedByOrganization;

    protected $guarded = [];

    protected $casts = [
        'type' => DeadlineType::class,
        'status' => DeadlineStatus::class,
        'due_at' => 'datetime',
        'met_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseFile::class, 'case_id');
    }
}
