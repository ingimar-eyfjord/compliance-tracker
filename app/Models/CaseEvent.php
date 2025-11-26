<?php

namespace App\Models;

use App\Enums\CaseEventType;
use App\Models\Concerns\HasUuid;
use App\Models\Concerns\ScopedByOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseEvent extends Model
{
    use HasFactory;
    use HasUuid;
    use ScopedByOrganization;

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'event' => CaseEventType::class,
        'data' => 'array',
        'created_at' => 'datetime',
    ];

    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseFile::class, 'case_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
