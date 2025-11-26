<?php

namespace App\Models;

use App\Enums\ActorType;
use App\Enums\AuditEvent;
use App\Models\Concerns\HasUuid;
use App\Models\Concerns\ScopedByOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory;
    use HasUuid;
    use ScopedByOrganization;

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'diff' => 'array',
        'actor_type' => ActorType::class,
        'event' => AuditEvent::class,
        'created_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }
}
