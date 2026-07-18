<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Media extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'media';

    protected $fillable = [
        'original_name',
        'file_name',
        'file_path',
        'file_url',
        'mime_type',
        'file_size',
        'file_size_human',
        'dimensions',
        'alt_text',
        'folder',
        'disk',
        'thumbnails',
        'uploaded_by',
        'used_in',
    ];

    protected $casts = [
        'dimensions' => 'array',
        'thumbnails' => 'array',
        'used_in' => 'array',
        'file_size' => 'integer',
        'uploaded_by' => 'integer',
    ];


    /**
     * ✅ Accessor للحصول على رابط نظيف
     */
    protected function cleanUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->file_path) {
                    return null;
                }

                // إذا كان file_url موجود ورابط كامل، استخدمه
                if ($this->file_url && str_starts_with($this->file_url, 'http')) {
                    return $this->file_url;
                }

                // وإلا، أنشئ الرابط من file_path
                return url('storage/' . $this->file_path);
            }
        );
    }

    /**
     * ✅ أو استخدم الطريقة القديمة (Laravel 8 وما قبل)
     */
    public function getCleanUrlAttribute()
    {
        if (!$this->file_path) {
            return null;
        }

        // إذا كان file_url موجود ورابط كامل
        if ($this->file_url && str_starts_with($this->file_url, 'http')) {
            return $this->file_url;
        }

        // أنشئ الرابط من file_path
        return url('storage/' . $this->file_path);
    }

    /**
     * العلاقة مع المستخدم
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Scopes
     */
    public function scopeByFolder($query, string $folder)
    {
        return $query->where('folder', $folder);
    }

    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    public function scopeDocuments($query)
    {
        return $query->whereIn('mime_type', [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }

    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }
}