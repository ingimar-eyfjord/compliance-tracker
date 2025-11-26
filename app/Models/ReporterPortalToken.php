<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\ScopedByOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReporterPortalToken extends Model
{
    use HasFactory;
    use HasUuid;
    use ScopedByOrganization;

    protected $guarded = [];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->whereNull('revoked_at')
            ->where('expires_at', '>', now());
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseFile::class, 'case_id');
    }
}
