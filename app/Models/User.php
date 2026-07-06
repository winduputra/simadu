<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'role_id', 'unit_kerja_id', 'nip', 'nama', 'email', 'password',
        'avatar_path', 'is_active', 'storage_quota', 'storage_used',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'storage_quota' => 'integer',
            'storage_used' => 'integer',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function folders(): HasMany
    {
        return $this->hasMany(Folder::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function documentVersions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function shares(): HasMany
    {
        return $this->hasMany(Share::class, 'shared_by');
    }

    public function sharedItems()
    {
        return $this->morphMany(Share::class, 'shared_to');
    }

    public function publicLinks(): HasMany
    {
        return $this->hasMany(PublicLink::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role?->slug === 'super-admin';
    }

    public function isAdminUnit(): bool
    {
        return $this->role?->slug === 'admin-unit';
    }

    public function hasQuotaFor(int $bytes): bool
    {
        if ($this->storage_quota === 0) return true;
        return ($this->storage_used + $bytes) <= $this->storage_quota;
    }
}
