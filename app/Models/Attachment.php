<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Concerns\ScopedByOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
    use HasFactory;
    use HasUuid;
    use ScopedByOrganization;

    protected $guarded = [];

    protected $casts = [
        'encrypted_meta' => 'array',
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}
