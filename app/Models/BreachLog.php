<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\ScopedByOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BreachLog extends Model
{
    use HasFactory;
    use HasUuid;
    use ScopedByOrganization;

    protected $guarded = [];

    protected $casts = [
        'impact' => 'array',
        'remediation' => 'array',
        'detected_at' => 'datetime',
        'notified_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseFile::class, 'case_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
