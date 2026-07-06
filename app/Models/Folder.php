<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Folder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_id', 'user_id', 'unit_kerja_id', 'nama', 'deskripsi', 'color', 'is_pinned',
    ];

    protected function casts(): array
    {
        return ['is_pinned' => 'boolean'];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function shares()
    {
        return $this->morphMany(Share::class, 'shareable');
    }

    public function publicLinks()
    {
        return $this->morphMany(PublicLink::class, 'linkable');
    }

    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
    }

    public function ancestors(): array
    {
        $ancestors = [];
        $current = $this->parent;
        while ($current) {
            array_unshift($ancestors, $current);
            $current = $current->parent;
        }
        return $ancestors;
    }
}
