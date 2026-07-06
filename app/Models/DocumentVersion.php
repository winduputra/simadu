<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentVersion extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'document_id', 'user_id', 'versi', 'nama_file', 'mime_type',
        'ukuran', 'storage_path', 'catatan', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'ukuran' => 'integer',
            'versi' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
