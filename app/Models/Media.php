<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Media extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'media';

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
        'used_in'    => 'array',
        'file_size'  => 'integer',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function scopeByFolder($query, string $folder)
    {
        return $query->where('folder', $folder);
    }

    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }
}