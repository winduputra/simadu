<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'folder_id', 'user_id', 'document_category_id', 'nama', 'nama_file_asli',
        'mime_type', 'ukuran', 'storage_path', 'current_version', 'is_pinned',
    ];

    protected function casts(): array
    {
        return [
            'ukuran' => 'integer',
            'current_version' => 'integer',
            'is_pinned' => 'boolean',
        ];
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documentCategory(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class)->orderByDesc('versi');
    }

    public function latestVersion()
    {
        return $this->hasOne(DocumentVersion::class)->ofMany('versi', 'max');
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

    public function formattedSize(): string
    {
        $bytes = $this->ukuran;
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
