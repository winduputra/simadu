<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Share extends Model
{
    protected $fillable = [
        'shareable_type', 'shareable_id', 'shared_by',
        'shared_to_type', 'shared_to_id', 'permission',
    ];

    public function shareable(): MorphTo
    {
        return $this->morphTo();
    }

    public function sharedTo(): MorphTo
    {
        return $this->morphTo('shared_to');
    }

    public function sharedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_by');
    }
}
