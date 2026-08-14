<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'folder_id',
        'name',
        'original_name',
        'storage_path',
        'mime_type',
        'size',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return '1 byte';
        } else {
            return '0 bytes';
        }
    }

    public function getIconCategoryAttribute(): string
    {
        $mime = $this->mime_type ?? '';
        $ext = strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));

        if (str_starts_with($mime, 'image/') || in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
            return 'image';
        }
        if (str_starts_with($mime, 'video/') || in_array($ext, ['mp4', 'mkv', 'avi', 'mov'])) {
            return 'video';
        }
        if (str_starts_with($mime, 'audio/') || in_array($ext, ['mp3', 'wav', 'ogg'])) {
            return 'audio';
        }
        if ($mime === 'application/pdf' || $ext === 'pdf') {
            return 'pdf';
        }
        if (in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'])) {
            return 'archive';
        }
        if (in_array($ext, ['doc', 'docx', 'txt', 'rtf', 'odt', 'pdf', 'xls', 'xlsx', 'ppt', 'pptx'])) {
            return 'document';
        }

        return 'file';
    }
}
