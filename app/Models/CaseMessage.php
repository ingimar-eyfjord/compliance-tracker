<?php

namespace App\Models;

use App\Enums\SenderType;
use App\Models\Concerns\HasUuid;
use App\Models\Concerns\ScopedByOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CaseMessage extends Model
{
    use HasFactory;
    use HasUuid;
    use ScopedByOrganization;

    protected $guarded = [];

    protected $casts = [
        'ciphertext' => 'array',
        'attachments' => 'array',
        'sender_type' => SenderType::class,
        'sent_at' => 'datetime',
    ];

    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseFile::class, 'case_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
