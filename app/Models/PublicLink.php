<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

class PublicLink extends Model
{
    protected $fillable = [
        'linkable_type', 'linkable_id', 'user_id', 'token', 'permission',
        'has_password', 'password', 'expires_at', 'max_access_count',
        'access_count', 'is_active',
    ];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'has_password' => 'boolean',
            'is_active' => 'boolean',
            'expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isMaxedOut(): bool
    {
        return $this->max_access_count && $this->access_count >= $this->max_access_count;
    }

    public function isAccessible(): bool
    {
        return $this->is_active && !$this->isExpired() && !$this->isMaxedOut();
    }

    public function incrementAccess(): void
    {
        $this->increment('access_count');
    }
}
