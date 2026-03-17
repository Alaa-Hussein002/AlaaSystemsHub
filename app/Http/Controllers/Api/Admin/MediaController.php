<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Media;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Media::orderBy('created_at', 'desc');
        if ($request->has('folder')) $query->where('folder', $request->folder);
        if ($request->has('type'))   $query->images();

        $media = $query->get();
        return $this->success($media);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file'   => 'required|file|max:10240',
            'folder' => 'nullable|string',
            'alt_text' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $folder = $request->get('folder', 'general');
        $path = $file->store("media/{$folder}", 'public');

        $media = Media::create([
            'original_name'  => $file->getClientOriginalName(),
            'file_name'      => basename($path),
            'file_path'      => $path,
            'file_url'       => Storage::url($path),
            'mime_type'      => $file->getMimeType(),
            'file_size'      => $file->getSize(),
            'file_size_human'=> $this->humanFileSize($file->getSize()),
            'alt_text'       => $request->alt_text,
            'folder'         => $folder,
            'disk'           => 'public',
            'uploaded_by'    => (string) auth()->user()->_id,
        ]);

        ActivityLog::log('create', 'media', "رفع ملف: {$file->getClientOriginalName()}");

        return $this->created($media, 'تم رفع الملف');
    }

    public function destroy(string $id)
    {
        $media = Media::find($id);
        if (!$media) return $this->notFound('الملف غير موجود');

        Storage::disk($media->disk ?? 'public')->delete($media->file_path);
        $media->delete();

        ActivityLog::log('delete', 'media', "حذف ملف: {$media->original_name}");
        return $this->success(null, 'تم الحذف');
    }

    private function humanFileSize($bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.1f %s", $bytes / pow(1024, $factor), $units[$factor]);
    }
}